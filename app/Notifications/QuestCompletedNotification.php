<?php

namespace App\Notifications;

use App\Models\Quest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuestCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Quest $quest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $reward = $this->quest->rewardLabel();

        return [
            'type'    => 'quest_completed',
            'title'   => '🎉 Nhiệm vụ hoàn thành!',
            'message' => "Bạn đã hoàn thành nhiệm vụ «{$this->quest->name}» và nhận được: {$reward}",
            'quest_id'   => $this->quest->id,
            'quest_name' => $this->quest->name,
            'reward_type' => $this->quest->reward_type,
            'reward_label' => $reward,
            'url'     => route('profile.quests'),
        ];
    }
}
