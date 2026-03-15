<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForumReplyNotification extends Notification
{
    use Queueable;

    public $thread;
    public $replier;

    /**
     * Create a new notification instance.
     */
    public function __construct($thread, $replier)
    {
        $this->thread = $thread;
        $this->replier = $replier;
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
            'message' => $this->replier->name . ' đã trả lời chủ đề "' . $this->thread->title . '" của bạn.',
            'url' => route('forum.show', $this->thread->slug) . '#reply',
            'avatar' => $this->replier->avatar ?? null,
        ];
    }
}
