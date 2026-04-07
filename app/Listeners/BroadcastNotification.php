<?php

namespace App\Listeners;

use App\Events\NewNotification;
use Illuminate\Notifications\Events\NotificationSent;

class BroadcastNotification
{
    /**
     * Handle the event.
     *
     * Listener này tự động broadcast mọi database notification qua WebSocket
     * mà không cần sửa từng Notification class.
     */
    public function handle(NotificationSent $event): void
    {
        // Chỉ broadcast khi notification được lưu vào database
        if ($event->channel !== 'database') {
            return;
        }

        $notifiable = $event->notifiable;

        // Chỉ broadcast cho User model
        if (!($notifiable instanceof \App\Models\User)) {
            return;
        }

        // Lấy notification vừa lưu từ DB
        $dbNotification = $notifiable->notifications()->latest()->first();

        if (!$dbNotification) {
            return;
        }

        broadcast(new NewNotification($notifiable->id, [
            'id' => $dbNotification->id,
            'data' => $dbNotification->data,
            'read_at' => null,
            'created_at' => $dbNotification->created_at->diffForHumans(),
            'is_new' => true,
        ]));
    }
}
