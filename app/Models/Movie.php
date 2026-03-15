<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    // Slug được tạo tự động từ 'title'
    protected $slugSource = 'title';

    protected $fillable = [
        'tmdb_id',
        'title',
        'original_title',
        'tagline',
        'synopsis',
        'poster',
        'backdrop',
        'trailer_url',
        'release_date',
        'runtime',
        'country',
        'language',
        'budget',
        'revenue',
        'avg_rating',
        'rating_count',
        'view_count',
        'is_approved',
        'status',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'is_approved' => 'boolean',
            'avg_rating' => 'decimal:1',
        ];
    }

    // ── Relationships ──

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function watchlistedBy()
    {
        return $this->belongsToMany(User::class, 'watchlists')->withPivot('status')->withTimestamps();
    }

    public function people()
    {
        return $this->belongsToMany(Person::class, 'movie_person')
            ->withPivot('role', 'character_name', 'display_order');
    }

    public function actors()
    {
        return $this->people()->wherePivot('role', 'actor')->orderByPivot('display_order');
    }

    public function directors()
    {
        return $this->people()->wherePivot('role', 'director');
    }

    public function writers()
    {
        return $this->people()->wherePivot('role', 'writer');
    }

    public function producers()
    {
        return $this->people()->wherePivot('role', 'producer');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function publishedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'published');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }


    // ── Scopes ──

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_approved', true);
    }

    // ── Helpers ──

    /**
     * Cập nhật avg_rating và rating_count từ các reviews published.
     */
    public function recalculateRating(): void
    {
        $stats = $this->reviews()
            ->where('status', 'published')
            ->whereNotNull('rating')
            ->selectRaw('AVG(rating) as avg, COUNT(*) as count')
            ->first();

        $this->update([
            'avg_rating' => round($stats->avg ?? 0, 1),
            'rating_count' => $stats->count ?? 0,
        ]);
    }
}
