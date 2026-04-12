<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvatarFrame extends Model
{
    protected $fillable = ['name', 'image_path', 'is_active'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_frame_inventory', 'frame_id', 'user_id')->withTimestamps();
    }
}
