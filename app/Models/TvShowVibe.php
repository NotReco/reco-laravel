<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TvShowVibe extends Model
{
    protected $fillable = ['tv_show_id', 'user_id', 'mood', 'tone'];

    public function tvShow()
    {
        return $this->belongsTo(TvShow::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
