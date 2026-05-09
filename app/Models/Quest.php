<?php

namespace App\Models;

use App\Enums\QuestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quest extends Model
{
    protected $fillable = [
        'name', 'slug', 'description',
        'type', 'target_value',
        'reward_type', 'reward_title_id', 'reward_frame_id',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'type'         => QuestType::class,
        'is_active'    => 'boolean',
        'target_value' => 'integer',
        'sort_order'   => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quest $quest) {
            if (empty($quest->slug)) {
                $quest->slug = Str::slug($quest->name);
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function rewardTitle()
    {
        return $this->belongsTo(UserTitle::class, 'reward_title_id');
    }

    public function rewardFrame()
    {
        return $this->belongsTo(AvatarFrame::class, 'reward_frame_id');
    }

    public function userProgress()
    {
        return $this->hasMany(UserQuestProgress::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function rewardLabel(): string
    {
        $labels = [];
        if (in_array($this->reward_type, ['title', 'both']) && $this->rewardTitle) {
            $labels[] = '🏷 ' . $this->rewardTitle->name;
        }
        if (in_array($this->reward_type, ['frame', 'both']) && $this->rewardFrame) {
            $labels[] = '🖼 ' . $this->rewardFrame->name;
        }
        return !empty($labels) ? implode(' + ', $labels) : '—';
    }
}
