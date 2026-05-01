<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    // Slug được tạo tự động từ 'name' (mặc định của HasSlug)

    protected $table = 'people';

    protected $fillable = [
        'tmdb_id',
        'name',
        'photo',
        'bio',
        'biography',
        'date_of_birth',
        'date_of_death',
        'nationality',
        'known_for',
        'gender',
        'place_of_birth',
        'also_known_as',
        'homepage',
        'imdb_id',
        'instagram_id',
        'twitter_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'  => 'date',
            'date_of_death'  => 'date',
            'also_known_as'  => 'array',
            'gender'         => 'integer',
        ];
    }

    /**
     * Trả về tiểu sử (ưu tiên biography mới, fallback sang bio cũ)
     */
    public function getBioTextAttribute(): string
    {
        return $this->biography ?? $this->bio ?? '';
    }

    /**
     * Nhãn giới tính bằng tiếng Việt
     */
    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            1 => 'Nữ',
            2 => 'Nam',
            3 => 'Phi nhị giới',
            default => '',
        };
    }

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_person')
            ->withPivot('role', 'character_name', 'display_order');
    }

    public function moviesAsActor()
    {
        return $this->movies()->wherePivot('role', 'actor');
    }

    public function moviesAsDirector()
    {
        return $this->movies()->wherePivot('role', 'director');
    }

    public function moviesAsWriter()
    {
        return $this->movies()->wherePivot('role', 'writer');
    }

    public function moviesAsProducer()
    {
        return $this->movies()->wherePivot('role', 'producer');
    }
}
