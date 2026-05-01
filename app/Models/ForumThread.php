<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    use HasSlug;

    protected $fillable = [
        'forum_category_id', 'user_id', 'title', 'slug',
        'content', 'views_count', 'is_pinned', 'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    // ── Sluggable ──

    protected $slugSource = 'title';

    // ── Relationships ──

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function latestReply()
    {
        return $this->hasOne(ForumReply::class)->latestOfMany();
    }

    // ── Scopes ──

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('updated_at');
    }

    // ── Helpers ──

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
