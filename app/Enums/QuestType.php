<?php

namespace App\Enums;

enum QuestType: string
{
    // Review & Bình luận
    case REVIEW_COUNT        = 'review_count';
    case COMMENT_COUNT       = 'comment_count';

    // Like nhận được trên review
    case LIKE_RECEIVED_COUNT = 'like_received_count';

    // Watchlist & Favorites
    case WATCHLIST_COUNT     = 'watchlist_count';
    case FAVORITE_COUNT      = 'favorite_count';

    // Cộng đồng
    case FOLLOWER_COUNT      = 'follower_count';
    case FOLLOWING_COUNT     = 'following_count';

    // Diễn đàn
    case FORUM_THREAD_COUNT  = 'forum_thread_count';
    case FORUM_REPLY_COUNT   = 'forum_reply_count';

    // Thời gian tài khoản
    case ACCOUNT_AGE_DAYS    = 'account_age_days';

    public function label(): string
    {
        return match($this) {
            self::REVIEW_COUNT        => 'Số review đã viết',
            self::COMMENT_COUNT       => 'Số bình luận',
            self::LIKE_RECEIVED_COUNT => 'Lượt thích nhận được',
            self::WATCHLIST_COUNT     => 'Phim trong watchlist',
            self::FAVORITE_COUNT      => 'Phim yêu thích',
            self::FOLLOWER_COUNT      => 'Số người theo dõi',
            self::FOLLOWING_COUNT     => 'Số người đang theo dõi',
            self::FORUM_THREAD_COUNT  => 'Số bài đăng diễn đàn',
            self::FORUM_REPLY_COUNT   => 'Số trả lời diễn đàn',
            self::ACCOUNT_AGE_DAYS    => 'Số ngày tài khoản',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::REVIEW_COUNT        => 'Viết đủ X reviews',
            self::COMMENT_COUNT       => 'Đăng đủ X bình luận',
            self::LIKE_RECEIVED_COUNT => 'Reviews của bạn nhận được tổng X lượt thích',
            self::WATCHLIST_COUNT     => 'Thêm X phim/series vào watchlist',
            self::FAVORITE_COUNT      => 'Thêm X phim/series vào yêu thích',
            self::FOLLOWER_COUNT      => 'Có X người theo dõi bạn',
            self::FOLLOWING_COUNT     => 'Theo dõi X người khác',
            self::FORUM_THREAD_COUNT  => 'Tạo X bài đăng trong diễn đàn',
            self::FORUM_REPLY_COUNT   => 'Trả lời X lần trong diễn đàn',
            self::ACCOUNT_AGE_DAYS    => 'Tài khoản tồn tại X ngày',
        };
    }
}
