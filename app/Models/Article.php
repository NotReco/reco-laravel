<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $slugSource = 'title';

    protected $fillable = [
        'user_id',
        'title',
        'subtitle',
        'slug',
        'content',
        'thumbnail',
        'rating_reco',
        'rating_imdb',
        'rating_metacritic',
        'rating_rotten_tomatoes',
        'rating_tmdb',
        'is_published',
        'published_at',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ── Scopes ──

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }

    // ── Relationships ──

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function hasExternalRatings(): bool
    {
        return collect([
            $this->rating_reco,
            $this->rating_imdb,
            $this->rating_metacritic,
            $this->rating_rotten_tomatoes,
            $this->rating_tmdb,
        ])->filter(fn ($v) => filled($v))->isNotEmpty();
    }
}
