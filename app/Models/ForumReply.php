<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use SoftDeletes;

    protected $fillable = ['forum_thread_id', 'user_id', 'content', 'parent_id'];

    // ── Relationships ──

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

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(ForumReplyLike::class);
    }



    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
