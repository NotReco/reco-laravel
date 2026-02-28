<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory, HasSlug;

    // Slug được tạo tự động từ 'name' (mặc định của HasSlug)

    protected $fillable = ['name'];

    public function movies()
    {
        return $this->morphedByMany(Movie::class, 'taggable');
    }

    public function reviews()
    {
        return $this->morphedByMany(Review::class, 'taggable');
    }

    /**
     * Tag không dùng SoftDeletes nên override withTrashed cho HasSlug.
     */
    public static function withTrashed()
    {
        return static::query();
    }
}
