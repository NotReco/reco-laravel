<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    // Slug được tạo tự động từ 'title'
    protected $slugSource = 'title';

    protected $fillable = [
        'user_id',
        'movie_id',
        'tv_show_id',
        'title',
        'thumbnail',
        'excerpt',
        'content',
        'rating',
        'is_spoiler',
        'status',
        'published_at',
        'view_count',
        'likes_count',
        'comments_count',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'is_spoiler' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ── Relationships ──

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function tvShow()
    {
        return $this->belongsTo(TvShow::class);
    }

    /**
     * Trả về model phim/series được đánh giá.
     */
    public function reviewable()
    {
        return $this->movie ?? $this->tvShow;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function rootComments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    // ── Scopes ──

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeHidden($query)
    {
        return $query->where('status', 'hidden');
    }

    public function scopeQuickRating($query)
    {
        return $query->whereNull('content');
    }

    public function scopeFullReview($query)
    {
        return $query->whereNotNull('content');
    }

    /**
     * Chỉ hiển thị review chưa bị ẩn.
     */
    public function scopeVisible($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Kiểm tra đây là chấm điểm nhanh (không có bài viết) hay review đầy đủ.
     */
    public function isQuickRating(): bool
    {
        return is_null($this->content);
    }

    /**
     * Kiểm tra review có đang bị ẩn không.
     */
    public function isHidden(): bool
    {
        return $this->status === 'hidden';
    }

    /**
     * Số lượt báo cáo đang chờ xử lý.
     */
    public function pendingReportsCount(): int
    {
        return $this->reports()->where('status', 'pending')->count();
    }

    /**
     * Polymorphic relation for reports.
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
