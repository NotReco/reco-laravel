<?php

namespace App\Observers;

use App\Enums\QuestType;
use App\Models\Comment;
use App\Services\QuestService;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        if ($comment->user) {
            app(QuestService::class)->check(
                $comment->user,
                QuestType::COMMENT_COUNT
            );
        }
    }
}
