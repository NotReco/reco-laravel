<?php

namespace App\Observers;

use App\Enums\QuestType;
use App\Models\Review;
use App\Services\QuestService;

class ReviewObserver
{
    public function created(Review $review): void
    {
        if ($review->user) {
            app(QuestService::class)->check(
                $review->user,
                QuestType::REVIEW_COUNT
            );
        }
    }

    public function deleted(Review $review): void
    {
        // Re-check count in case deletion drops below threshold
        // (không xóa completion đã có)
        if ($review->user) {
            app(QuestService::class)->check(
                $review->user,
                QuestType::REVIEW_COUNT
            );
        }
    }
}
