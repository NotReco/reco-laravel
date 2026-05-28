<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedWord extends Model
{
    protected $fillable = [
        'word',
        'severity',
        'action',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
