<?php

namespace App\Observers;

use App\Enums\QuestType;
use App\Models\ForumThread;
use App\Services\QuestService;

class ForumThreadObserver
{
    public function created(ForumThread $thread): void
    {
        if ($thread->user) {
            app(QuestService::class)->check(
                $thread->user,
                QuestType::FORUM_THREAD_COUNT
            );
        }
    }
}
