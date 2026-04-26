<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ForumMentionNotification extends Notification
{
    use Queueable;

    public $thread;
    public $mentioner;

    /**
     * Create a new notification instance.
     */
    public function __construct($thread, $mentioner)
    {
        $this->thread = $thread;
        $this->mentioner = $mentioner;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->mentioner->name . ' đã nhắc đến bạn trong chủ đề "' . $this->thread->title . '".',
            'url' => route('forum.show', $this->thread->slug) . '#reply',
            'avatar' => $this->mentioner->avatar ?? null,
        ];
    }
}
