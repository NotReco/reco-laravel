<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumReplyLike extends Model
{
    protected $fillable = ['user_id', 'forum_reply_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forumReply()
    {
        return $this->belongsTo(ForumReply::class);
    }
}
