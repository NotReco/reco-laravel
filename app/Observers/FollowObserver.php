<?php

namespace App\Observers;

use App\Enums\QuestType;
use App\Models\Follow;
use App\Services\QuestService;

class FollowObserver
{
    public function created(Follow $follow): void
    {
        $service = app(QuestService::class);

        // Người được follow → kiểm tra follower_count quest
        if ($follow->following) {
            $service->check($follow->following, QuestType::FOLLOWER_COUNT);
        }

        // Người follow → kiểm tra following_count quest
        if ($follow->follower) {
            $service->check($follow->follower, QuestType::FOLLOWING_COUNT);
        }
    }

    public function deleted(Follow $follow): void
    {
        // Re-check (không xóa completion đã đạt)
        if ($follow->following) {
            app(QuestService::class)->check($follow->following, QuestType::FOLLOWER_COUNT);
        }
    }
}
