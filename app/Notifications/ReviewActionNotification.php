<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewActionNotification extends Notification
{
    use Queueable;

    public string $action; // 'hidden' | 'deleted'
    public ?string $reviewTitle;
    public ?string $contentTitle; // tên phim/series
    public ?string $reason;

    /**
     * @param string      $action       'hidden' hoặc 'deleted'
     * @param Review|null $review       Model review (null nếu đã xóa)
     * @param string|null $contentTitle Tên phim/series
     * @param string|null $reason       Lý do (tuỳ chọn)
     */
    public function __construct(
        string $action,
        ?string $reviewTitle,
        ?string $contentTitle,
        ?string $reason = null
    ) {
        $this->action       = $action;
        $this->reviewTitle  = $reviewTitle;
        $this->contentTitle = $contentTitle;
        $this->reason       = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $contentLabel = $this->contentTitle ? " về \"{$this->contentTitle}\"" : '';

        if ($this->action === 'hidden') {
            $message = "Đánh giá{$contentLabel} của bạn đã bị ẩn do vi phạm quy tắc cộng đồng.";
        } else {
            $message = "Đánh giá{$contentLabel} của bạn đã bị xóa do vi phạm quy tắc cộng đồng.";
        }

        return [
            'type'         => 'review_action',
            'action'       => $this->action,
            'message'      => $message,
            'review_title' => $this->reviewTitle,
            'reason'       => $this->reason,
            'url'          => null, // review đã bị ẩn/xóa nên không link được
            'avatar'       => null, // dùng icon hệ thống
            'icon'         => $this->action === 'hidden' ? 'shield' : 'trash',
        ];
    }
}
