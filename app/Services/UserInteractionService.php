<?php

namespace App\Services;

use App\Models\UserInteraction;
use App\Models\ViewHistory;
use App\Models\SearchHistory;
use Illuminate\Database\Eloquent\Model;

class UserInteractionService
{
    /**
     * Ghi nhận lượt xem phim/TV show.
     */
    public function recordView($user, Model $viewable, string $source = 'detail')
    {
        $ip = request()->ip();
        $tenMinutesAgo = now()->subMinutes(10);
        $viewableType = get_class($viewable);

        // Kiểm tra xem đã có log trong 10 phút chưa
        $query = ViewHistory::where('viewable_type', $viewableType)
            ->where('viewable_id', $viewable->id)
            ->where('viewed_at', '>=', $tenMinutesAgo);

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->whereNull('user_id')->where('ip_address', $ip);
        }

        if ($query->exists()) {
            return; // Đã ghi nhận trong 10 phút gần đây
        }

        // 1. Tạo view_histories
        ViewHistory::create([
            'user_id' => $user ? $user->id : null,
            'viewable_type' => $viewableType,
            'viewable_id' => $viewable->id,
            'source' => $source,
            'viewed_at' => now(),
            'ip_address' => $ip,
        ]);

        // 2. Tạo user_interactions (nếu có user)
        if ($user) {
            // Kiểm tra trùng lặp trong user_interactions
            $recentInteraction = UserInteraction::where('user_id', $user->id)
                ->where('interactable_type', $viewableType)
                ->where('interactable_id', $viewable->id)
                ->where('type', 'view')
                ->where('created_at', '>=', $tenMinutesAgo)
                ->exists();

            if (!$recentInteraction) {
                $this->recordInteraction($user, $viewable, 'view', 1, ['source' => $source]);
            }
        }
    }

    /**
     * Ghi nhận lịch sử tìm kiếm.
     */
    public function recordSearch($user, string $keyword, int $resultsCount)
    {
        $keyword = trim(mb_strtolower($keyword, 'UTF-8'));
        if (mb_strlen($keyword, 'UTF-8') < 3) {
            return;
        }

        $ip = request()->ip();
        $tenMinutesAgo = now()->subMinutes(10);

        // Kiểm tra xem đã có log search này trong 10 phút chưa
        $query = SearchHistory::where('keyword', $keyword)
            ->where('searched_at', '>=', $tenMinutesAgo);

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->whereNull('user_id')->where('ip_address', $ip);
        }

        if ($query->exists()) {
            return;
        }

        // Lưu search_histories
        SearchHistory::create([
            'user_id' => $user ? $user->id : null,
            'keyword' => $keyword,
            'results_count' => $resultsCount,
            'searched_at' => now(),
            'ip_address' => $ip,
        ]);

        if ($user) {
            $recentInteraction = UserInteraction::where('user_id', $user->id)
                ->where('type', 'search')
                ->where('created_at', '>=', $tenMinutesAgo)
                ->whereJsonContains('metadata->keyword', $keyword)
                ->exists();

            if (!$recentInteraction) {
                $this->recordInteraction($user, null, 'search', 2, ['keyword' => $keyword, 'results_count' => $resultsCount]);
            }
        }
    }

    /**
     * Ghi nhận tương tác chung.
     */
    public function recordInteraction($user, ?Model $interactable, string $type, float $score = 0, array $metadata = [])
    {
        if (!$user) return;

        UserInteraction::create([
            'user_id' => $user->id,
            'interactable_type' => $interactable ? get_class($interactable) : null,
            'interactable_id' => $interactable ? $interactable->id : null,
            'type' => $type,
            'score' => $score,
            'metadata' => $metadata,
        ]);
    }
}
