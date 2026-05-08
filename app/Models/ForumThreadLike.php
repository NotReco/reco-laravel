<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumThreadLike extends Model
{
    protected $fillable = ['user_id', 'forum_thread_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forumThread()
    {
        return $this->belongsTo(ForumThread::class);
    }
}
