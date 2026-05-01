<?php

namespace App\Observers;

use App\Enums\QuestType;
use App\Models\Like;
use App\Models\Review;
use App\Services\QuestService;

class LikeObserver
{
    public function created(Like $like): void
    {
        // Khi like một review → kiểm tra quest của CHỦ review (người nhận like)
        if ($like->likeable_type === Review::class) {
            $review = $like->likeable;
            if ($review && $review->user) {
                app(QuestService::class)->check(
                    $review->user,
                    QuestType::LIKE_RECEIVED_COUNT
                );
            }
        }
    }
}
