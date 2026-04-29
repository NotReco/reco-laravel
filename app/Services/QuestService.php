<?php

namespace App\Services;

use App\Enums\QuestType;
use App\Models\Quest;
use App\Models\User;
use App\Models\UserQuestProgress;
use App\Notifications\QuestCompletedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestService
{
    /**
     * Điểm vào chính: gọi sau mỗi hành động của user.
     * Chỉ kiểm tra các quest thuộc $types được chỉ định.
     */
    public function check(User $user, QuestType ...$types): void
    {
        $quests = Quest::active()
            ->whereIn('type', array_map(fn($t) => $t->value, $types))
            ->with(['rewardTitle', 'rewardFrame'])
            ->orderBy('sort_order')
            ->get();

        if ($quests->isEmpty()) return;

        foreach ($quests as $quest) {
            $this->evaluateQuest($user, $quest);
        }
    }

    /**
     * Kiểm tra toàn bộ quest cho 1 user (dùng khi rebuild).
     */
    public function checkAll(User $user): void
    {
        $types = array_map(fn($t) => $t->value, QuestType::cases());
        $quests = Quest::active()
            ->whereIn('type', $types)
            ->with(['rewardTitle', 'rewardFrame'])
            ->get();

        foreach ($quests as $quest) {
            $this->evaluateQuest($user, $quest);
        }
    }

    // ── Core logic ──────────────────────────────────────────────────────────

    private function evaluateQuest(User $user, Quest $quest): void
    {
        // Lấy hoặc tạo progress record
        $progress = UserQuestProgress::firstOrCreate(
            ['user_id' => $user->id, 'quest_id' => $quest->id],
            ['progress' => 0]
        );

        // Đã hoàn thành rồi → bỏ qua
        if ($progress->isCompleted()) return;

        // Tính giá trị thực tế
        $currentValue = $this->getCurrentValue($user, $quest->type);

        // Cập nhật progress
        $progress->progress = $currentValue;
        $progress->save();

        // Kiểm tra đã đủ điều kiện chưa
        if ($currentValue >= $quest->target_value) {
            $this->completeQuest($user, $quest, $progress);
        }
    }

    private function getCurrentValue(User $user, QuestType $type): int
    {
        return match($type) {
            QuestType::REVIEW_COUNT        => $user->reviews()->count(),
            QuestType::COMMENT_COUNT       => $user->comments()->count(),
            QuestType::LIKE_RECEIVED_COUNT => $this->getLikesReceived($user),
            QuestType::WATCHLIST_COUNT     => $this->getWatchlistCount($user),
            QuestType::FAVORITE_COUNT      => $this->getFavoriteCount($user),
            QuestType::FOLLOWER_COUNT      => $user->followers()->count(),
            QuestType::FOLLOWING_COUNT     => $user->following()->count(),
            QuestType::FORUM_THREAD_COUNT  => \App\Models\ForumThread::where('user_id', $user->id)->count(),
            QuestType::FORUM_REPLY_COUNT   => \App\Models\ForumReply::where('user_id', $user->id)->count(),
            QuestType::ACCOUNT_AGE_DAYS    => (int) $user->created_at->diffInDays(now()),
        };
    }

    private function getLikesReceived(User $user): int
    {
        return \App\Models\Like::whereHasMorph(
            'likeable', [\App\Models\Review::class],
            fn($q) => $q->where('user_id', $user->id)
        )->count();
    }

    private function getWatchlistCount(User $user): int
    {
        $movies  = $user->watchlists()->count();
        $tvShows = $user->tvShowWatchlists()->count();
        return $movies + $tvShows;
    }

    private function getFavoriteCount(User $user): int
    {
        $movies  = $user->favorites()->count();
        $tvShows = $user->tvShowFavorites()->count();
        return $movies + $tvShows;
    }

    // ── Reward ──────────────────────────────────────────────────────────────

    private function completeQuest(User $user, Quest $quest, UserQuestProgress $progress): void
    {
        DB::transaction(function () use ($user, $quest, $progress) {
            $now = now();

            // Đánh dấu hoàn thành nhưng CHƯA nhận thưởng
            $progress->update([
                'completed_at' => $now,
                'progress'     => $quest->target_value,
            ]);

            // Gửi notification báo hiệu có thể nhận thưởng
            try {
                $user->notify(new QuestCompletedNotification($quest));
            } catch (\Throwable $e) {
                Log::warning("Quest notification failed for user {$user->id}: " . $e->getMessage());
            }
        });
    }

    /**
     * User gọi hàm này để tự nhận phần thưởng (Claim)
     */
    public function claimReward(User $user, Quest $quest): bool
    {
        $progress = UserQuestProgress::where('user_id', $user->id)
            ->where('quest_id', $quest->id)
            ->first();

        // Kiểm tra xem đã hoàn thành và chưa nhận chưa
        if (!$progress || !$progress->isCompleted() || $progress->isRewarded()) {
            return false;
        }

        DB::transaction(function () use ($user, $quest, $progress) {
            // Đánh dấu đã nhận
            $progress->update([
                'rewarded_at' => now(),
            ]);

            // Phát phần thưởng vào inventory
            if ($quest->reward_type === 'title' && $quest->reward_title_id) {
                $user->titles()->syncWithoutDetaching([$quest->reward_title_id]);
            } elseif ($quest->reward_type === 'frame' && $quest->reward_frame_id) {
                $user->frames()->syncWithoutDetaching([$quest->reward_frame_id]);
            }
        });

        return true;
    }
}
