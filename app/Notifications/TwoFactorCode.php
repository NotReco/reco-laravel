<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCode extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔐 Mã xác thực đăng nhập — Reco')
            ->greeting("Xin chào {$notifiable->name},")
            ->line('Bạn vừa đăng nhập vào tài khoản quản trị. Đây là mã xác thực của bạn:')
            ->line("**{$notifiable->two_factor_code}**")
            ->line('Mã này có hiệu lực trong **10 phút**.')
            ->line('Nếu bạn không thực hiện đăng nhập này, vui lòng đổi mật khẩu ngay.')
            ->salutation('— Hệ thống Reco');
    }
}
