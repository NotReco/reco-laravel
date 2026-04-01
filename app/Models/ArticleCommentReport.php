<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCommentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_comment_id',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->belongsTo(ArticleComment::class, 'article_comment_id');
    }
}
