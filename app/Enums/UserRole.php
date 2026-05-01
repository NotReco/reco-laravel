<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case TESTER = 'tester';
    case MODERATOR = 'moderator';
    case ADMIN = 'admin';

    /**
     * Tên hiển thị tiếng Việt.
     */
    public function label(): string
    {
        return match ($this) {
            self::USER => 'Người dùng',
            self::TESTER => 'Tester',
            self::MODERATOR => 'Kiểm duyệt viên',
            self::ADMIN => 'Quản trị viên',
        };
    }

    /**
     * Màu badge cho UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::USER => 'gray',
            self::TESTER => 'blue',
            self::MODERATOR => 'amber',
            self::ADMIN => 'red',
        };
    }

    /**
     * Cấp độ quyền hạn (số càng cao = quyền càng lớn).
     */
    public function level(): int
    {
        return match ($this) {
            self::USER => 1,
            self::TESTER => 1, // Tester = User về quyền
            self::MODERATOR => 2,
            self::ADMIN => 3,
        };
    }

    /**
     * Kiểm tra role này có quyền >= role kia không.
     */
    public function isAtLeast(self $role): bool
    {
        return $this->level() >= $role->level();
    }
}
