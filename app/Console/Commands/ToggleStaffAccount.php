<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class ToggleStaffAccount extends Command
{
    protected $signature = 'user:toggle {email} {--activate : Kích hoạt tài khoản} {--deactivate : Khóa tài khoản}';
    protected $description = 'Bật/tắt tài khoản staff (moderator) — tương tự /op trong Minecraft';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Không tìm thấy tài khoản: {$email}");
            return self::FAILURE;
        }

        // Hiển thị thông tin
        $this->info("👤 Tài khoản: {$user->name}");
        $this->info("📧 Email: {$user->email}");
        $this->info("🔑 Role: {$user->role->label()} ({$user->role->value})");
        $this->info("📌 Trạng thái: " . ($user->is_active ? '✅ Đang hoạt động' : '🔒 Đang khóa'));
        $this->newLine();

        if ($this->option('activate')) {
            $user->update(['is_active' => true]);
            $this->info("✅ Đã kích hoạt tài khoản {$user->name}!");
            return self::SUCCESS;
        }

        if ($this->option('deactivate')) {
            $user->update(['is_active' => false]);
            $this->warn("🔒 Đã khóa tài khoản {$user->name}!");
            return self::SUCCESS;
        }

        // Toggle nếu không có option
        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        if ($newStatus) {
            $this->info("✅ Đã kích hoạt tài khoản {$user->name}!");
        } else {
            $this->warn("🔒 Đã khóa tài khoản {$user->name}!");
        }

        return self::SUCCESS;
    }
}
