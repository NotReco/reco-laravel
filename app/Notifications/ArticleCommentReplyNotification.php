<?php

namespace App\Notifications;

use App\Models\ArticleComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ArticleCommentReplyNotification extends Notification
{
    use Queueable;

    public ArticleComment $reply;
    public string $replierName;

    public function __construct(ArticleComment $reply, string $replierName)
    {
        $this->reply = $reply;
        $this->replierName = $replierName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'article_comment_reply',
            'action'       => 'reply',
            'message'      => $this->reply->parent_id 
                                ? "{$this->replierName} đã trả lời bình luận của bạn trong tin tức."
                                : "{$this->replierName} đã bình luận bài viết tin tức của bạn.",
            'url'          => route('articles.show', $this->reply->article->slug) . '#comment-' . $this->reply->id,
            'avatar'       => $this->reply->user->avatar ?? null,
            'icon'         => 'message-square',
        ];
    }
}
