<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuestProgress extends Model
{
    protected $table = 'user_quest_progress';

    protected $fillable = [
        'user_id', 'quest_id', 'progress', 'completed_at', 'rewarded_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'rewarded_at'  => 'datetime',
        'progress'     => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isRewarded(): bool
    {
        return $this->rewarded_at !== null;
    }

    /** Phần trăm hoàn thành (0-100) */
    public function percentageFor(Quest $quest): int
    {
        if ($quest->target_value <= 0) return 100;
        return min(100, (int) round(($this->progress / $quest->target_value) * 100));
    }
}
