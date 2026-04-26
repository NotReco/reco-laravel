<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TvShow extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

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
        'first_air_date',
        'last_air_date',
        'number_of_seasons',
        'number_of_episodes',
        'episode_runtime',
        'networks',
        'type',
        'tmdb_status',
        'country',
        'language',
        'avg_rating',
        'rating_count',
        'view_count',
        'is_approved',
        'status',
        'is_featured',
        'featured_order',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'first_air_date'  => 'date',
            'last_air_date'   => 'date',
            'is_approved'     => 'boolean',
            'is_featured'     => 'boolean',
            'avg_rating'      => 'decimal:1',
            'networks'        => 'array',   // JSON auto-cast
        ];
    }

    // ── Helpers ──

    /**
     * Lấy tên + logo của network đầu tiên.
     */
    public function primaryNetwork(): ?array
    {
        $networks = $this->networks;
        return $networks[0] ?? null;
    }

    /**
     * Trạng thái hiển thị thân thiện (Vietnamese).
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->tmdb_status) {
            'Returning Series'  => 'Đang phát sóng',
            'Ended'             => 'Đã kết thúc',
            'Canceled'          => 'Đã hủy',
            'In Production'     => 'Đang sản xuất',
            'Planned'           => 'Đã lên kế hoạch',
            'Pilot'             => 'Pilot',
            default             => $this->tmdb_status ?? 'Không rõ',
        };
    }

    /**
     * Cập nhật avg_rating và rating_count từ reviews published.
     */
    public function recalculateRating(): void
    {
        $stats = $this->reviews()
            ->where('status', 'published')
            ->whereNotNull('rating')
            ->selectRaw('AVG(rating) as avg, COUNT(*) as count')
            ->first();

        $this->update([
            'avg_rating'   => round($stats->avg ?? 0, 1),
            'rating_count' => $stats->count ?? 0,
        ]);
    }

    // ── Relationships ──

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'tv_show_genre');
    }

    public function people()
    {
        return $this->belongsToMany(Person::class, 'tv_show_person')
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

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'tv_show_id');
    }

    public function watchlistedBy()
    {
        return $this->belongsToMany(User::class, 'watchlists', 'tv_show_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'tv_show_id');
    }

    public function vibes()
    {
        return $this->hasMany(TvShowVibe::class);
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
}
