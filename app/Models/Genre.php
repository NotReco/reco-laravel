<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    // Slug được tạo tự động từ 'name' (mặc định của HasSlug)

    protected $fillable = ['tmdb_id', 'name', 'description', 'icon'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_genre');
    }
}
