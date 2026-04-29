<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasSlug, HasRoles;

    // Slug được tạo tự động từ 'name'
    protected $slugSource = 'name';

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'cover_photo',
        'bio',
        'date_of_birth',
        'pronouns',
        'location',
        'website',
        'role',
        'is_active',
        'last_login_at',
        'two_factor_code',
        'two_factor_expires_at',
        'two_factor_enabled',
        'two_factor_remember_enabled',
        'two_factor_trusted_token_hash',
        'two_factor_trusted_until',
        'notification_preferences',
        'movie_quote',
        'reputation_score',
        'active_title_id',
        'active_frame_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'role' => UserRole::class,
            'two_factor_expires_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_remember_enabled' => 'boolean',
            'two_factor_trusted_until' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    // ── Notifications ──

    public function notify($instance)
    {
        $type = get_class($instance);
        $preferences = $this->notification_preferences ?? [];
        
        if (in_array($type, $preferences['disabled_types'] ?? [])) {
            return;
        }

        app(\Illuminate\Contracts\Notifications\Dispatcher::class)->send($this, $instance);
    }

    // ── Relationships ──

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function favorites()
    {
        return $this->belongsToMany(Movie::class, 'favorites')->whereNotNull('movie_id')->withTimestamps();
    }

    public function tvShowFavorites()
    {
        return $this->belongsToMany(\App\Models\TvShow::class, 'favorites', 'user_id', 'tv_show_id')->whereNotNull('tv_show_id')->withTimestamps();
    }

    public function watchlists()
    {
        return $this->belongsToMany(Movie::class, 'watchlists')->whereNotNull('movie_id')->withPivot('status')->withTimestamps();
    }

    public function tvShowWatchlists()
    {
        return $this->belongsToMany(\App\Models\TvShow::class, 'watchlists', 'user_id', 'tv_show_id')->whereNotNull('tv_show_id')->withPivot('status')->withTimestamps();
    }

    public function vibes()
    {
        return $this->hasMany(MovieVibe::class);
    }

    public function tvShowVibes()
    {
        return $this->hasMany(\App\Models\TvShowVibe::class);
    }

    public function activeTitle()
    {
        return $this->belongsTo(UserTitle::class, 'active_title_id');
    }

    public function activeFrame()
    {
        return $this->belongsTo(AvatarFrame::class, 'active_frame_id');
    }

    public function titles()
    {
        return $this->belongsToMany(UserTitle::class, 'user_title_inventory', 'user_id', 'title_id')->withTimestamps();
    }

    public function frames()
    {
        return $this->belongsToMany(AvatarFrame::class, 'user_frame_inventory', 'user_id', 'frame_id')->withTimestamps();
    }

    public function topMovies()
    {
        return $this->belongsToMany(Movie::class, 'user_top_movies')->withPivot('order')->orderBy('user_top_movies.order')->withTimestamps();
    }
    public function articles()
    {
        return $this->hasMany(Article::class);
    }


    /**
     * Tùy chỉnh hệ thống gửi Email khôi phục mật khẩu.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    // ── Role Helpers ──

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role === UserRole::MODERATOR;
    }

    public function isTester(): bool
    {
        return $this->role === UserRole::TESTER;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    /**
     * Staff = Moderator hoặc Admin (có quyền vào admin panel).
     */
    public function isStaff(): bool
    {
        return $this->role->level() >= UserRole::MODERATOR->level();
    }

    /**
     * Kiểm tra user có quyền >= role cho trước.
     */
    public function hasRoleLevel(UserRole $role): bool
    {
        return $this->role->isAtLeast($role);
    }

    // ── Permission Helpers ──

    /**
     * Có quyền quản lý user khác (ban, đổi role thấp hơn...).
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Có quyền quản lý content (phim, review, comment...).
     */
    public function canManageContent(): bool
    {
        return $this->isStaff();
    }

    /**
     * Có quyền duyệt/từ chối review.
     */
    public function canModerateReviews(): bool
    {
        return $this->isStaff();
    }

    /**
     * Có quyền xóa vĩnh viễn (force delete).
     */
    public function canForceDelete(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Có quyền thay đổi cài đặt hệ thống.
     */
    public function canManageSettings(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Có quyền import dữ liệu TMDb.
     */
    public function canImportData(): bool
    {
        return $this->isAdmin();
    }

    // ── Two-Factor Authentication ──

    /**
     * Tạo mã 2FA 6 chữ số, hết hạn sau 10 phút.
     */
    public function generateTwoFactorCode(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        return $code;
    }

    /**
     * Xóa mã 2FA sau khi xác thực thành công.
     */
    public function clearTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);
    }

    /**
     * Kiểm tra cần 2FA không (admin + moderator đang active).
     */
    public function requiresTwoFactor(): bool
    {
        return (bool) $this->two_factor_enabled;
    }
}

