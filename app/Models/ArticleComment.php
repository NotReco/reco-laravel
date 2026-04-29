<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'article_id',
        'parent_id',
        'content',
        'is_edited',
    ];

    protected function casts(): array
    {
        return [
            'is_edited' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function parent()
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ArticleComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(ArticleCommentLike::class, 'article_comment_id');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function isLikedBy($user)
    {
        if (!$user) {
            return false;
        }
        return $this->likes->contains('user_id', $user->id);
    }
}
