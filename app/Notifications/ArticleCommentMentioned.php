<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ArticleCommentMentioned extends Notification
{
    use Queueable;

    protected $comment;
    protected $mentionerName;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $mentionerName)
    {
        $this->comment = $comment;
        $this->mentionerName = $mentionerName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->mentionerName . ' đã nhắc đến bạn trong một bình luận.',
            'url' => route('news.show', ['article' => $this->comment->article]) . '#comment-' . $this->comment->id,
        ];
    }
}
