<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousLoginNotification extends Notification
{
    use Queueable;

    /**
     * 'warning' = cảnh báo (lần 3), 'locked' = đã khóa (lần 5+)
     */
    public string $type;

    /**
     * Địa chỉ IP thực hiện đăng nhập sai.
     */
    public string $ip;

    /**
     * Số phút tài khoản bị khóa (chỉ dùng khi type = 'locked').
     */
    public int $lockMinutes;

    /**
     * Số lần đã nhập sai.
     */
    public int $failedCount;

    public function __construct(string $type, string $ip, int $failedCount, int $lockMinutes = 30)
    {
        $this->type        = $type;
        $this->ip          = $ip;
        $this->failedCount = $failedCount;
        $this->lockMinutes = $lockMinutes;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl  = route('password.request');
        $subject   = $this->type === 'locked'
            ? 'Cảnh báo: Tài khoản RecoDB của bạn đã bị khóa tạm thời'
            : 'Cảnh báo: Phát hiện đăng nhập đáng ngờ trên tài khoản RecoDB';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.suspicious-login', [
                'userName'    => $notifiable->name,
                'type'        => $this->type,
                'ip'          => $this->ip,
                'failedCount' => $this->failedCount,
                'lockMinutes' => $this->lockMinutes,
                'resetUrl'    => $resetUrl,
                'time'        => now()->format('H:i, d/m/Y'),
            ]);
    }
}
