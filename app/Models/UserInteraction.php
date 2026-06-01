<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserInteraction extends Model
{
    protected $fillable = [
        'user_id',
        'interactable_id',
        'interactable_type',
        'type',
        'score',
        'metadata',
    ];

    protected $casts = [
        'score'    => 'decimal:2', // Đổi từ float → decimal để tránh sai số dấu phẩy động
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interactable(): MorphTo
    {
        return $this->morphTo();
    }
}
