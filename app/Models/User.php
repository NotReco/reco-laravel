<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'cover_photo',
        'bio',
        'date_of_birth',
        'gender',
        'location',
        'website',
        'role',
        'is_active',
        'last_login_at',
        'two_factor_code',
        'two_factor_expires_at',
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
        ];
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
        return $this->belongsToMany(Movie::class, 'favorites')->withTimestamps();
    }

    public function watchlists()
    {
        return $this->belongsToMany(Movie::class, 'watchlists')->withPivot('status')->withTimestamps();
    }


    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
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
        return $this->isAdmin();
    }
}

