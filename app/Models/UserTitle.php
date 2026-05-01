<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTitle extends Model
{
    protected $fillable = ['name', 'color_hex', 'description', 'is_active'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_title_inventory', 'title_id', 'user_id')->withTimestamps();
    }
}
