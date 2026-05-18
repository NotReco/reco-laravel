<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewCommentNotification extends Notification
{
    use Queueable;

    public Comment $reply;
    public string $replierName;

    public function __construct(Comment $reply, string $replierName)
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
        // Kiểm tra xem review là của movie hay tv_show để tạo link cho đúng
        $review = $this->reply->review;
        $url = '#';
        
        if ($review) {
            if ($review->movie_id) {
                $url = route('movies.show', $review->movie->slug) . '#review-' . $review->id;
            } elseif ($review->tv_show_id) {
                $url = route('tv.show', $review->tvShow->slug) . '#review-' . $review->id;
            }
        }

        return [
            'type'         => 'review_comment',
            'action'       => 'reply',
            'message'      => $this->reply->parent_id 
                                ? "{$this->replierName} đã trả lời bình luận của bạn." 
                                : "{$this->replierName} đã bình luận về bài đánh giá của bạn.",
            'url'          => $url,
            'avatar'       => $this->reply->user->avatar ?? null,
            'icon'         => 'message-square',
        ];
    }
}
