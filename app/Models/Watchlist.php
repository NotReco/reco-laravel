<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'movie_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // ── Scopes ──

    public function scopeWantToWatch($query)
    {
        return $query->where('status', 'want_to_watch');
    }

    public function scopeWatched($query)
    {
        return $query->where('status', 'watched');
    }

    public function scopeWatching($query)
    {
        return $query->where('status', 'watching');
    }
}
