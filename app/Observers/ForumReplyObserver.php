<?php

namespace App\Observers;

use App\Enums\QuestType;
use App\Models\ForumReply;
use App\Services\QuestService;

class ForumReplyObserver
{
    public function created(ForumReply $reply): void
    {
        if ($reply->user) {
            app(QuestService::class)->check(
                $reply->user,
                QuestType::FORUM_REPLY_COUNT
            );
        }
    }
}
