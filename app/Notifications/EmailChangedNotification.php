<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangedNotification extends Notification
{
    use Queueable;

    /**
     * Địa chỉ email MỚI mà người dùng vừa đổi sang.
     */
    public string $newEmail;

    /**
     * Địa chỉ email CŨ và tên người dùng (để hiển thị trong email cảnh báo).
     */
    public string $oldEmail;
    public string $userName;

    public function __construct(string $newEmail, string $oldEmail, string $userName)
    {
        $this->newEmail  = $newEmail;
        $this->oldEmail  = $oldEmail;
        $this->userName  = $userName;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('password.request');

        return (new MailMessage)
            ->subject('Cảnh báo: Địa chỉ email tài khoản đã thay đổi — RecoDB')
            ->view('emails.email-changed', [
                'userName' => $this->userName,
                'oldEmail' => $this->oldEmail,
                'newEmail' => $this->newEmail,
                'resetUrl' => $resetUrl,
            ]);
    }
}
