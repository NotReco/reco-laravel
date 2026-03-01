<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->defineGates();
    }

    /**
     * Định nghĩa Authorization Gates.
     */
    protected function defineGates(): void
    {
        // ── Admin Panel ──
        // Chỉ staff (moderator + admin) mới được vào admin panel
        Gate::define('access-admin', fn(User $user) => $user->isStaff());

        // ── Quản lý Users ──
        // Chỉ admin mới quản lý được user (ban, đổi role, xóa)
        Gate::define('manage-users', fn(User $user) => $user->canManageUsers());

        // ── Quản lý Content ──
        // Staff có quyền CRUD phim, xem/duyệt review, xóa comment
        Gate::define('manage-movies', fn(User $user) => $user->canManageContent());
        Gate::define('manage-reviews', fn(User $user) => $user->canModerateReviews());
        Gate::define('manage-comments', fn(User $user) => $user->canManageContent());
        Gate::define('manage-genres', fn(User $user) => $user->canManageContent());
        Gate::define('manage-people', fn(User $user) => $user->canManageContent());
        Gate::define('manage-tags', fn(User $user) => $user->canManageContent());

        // ── Hệ thống ──
        // Chỉ admin mới được dùng
        Gate::define('manage-settings', fn(User $user) => $user->canManageSettings());
        Gate::define('import-data', fn(User $user) => $user->canImportData());
        Gate::define('force-delete', fn(User $user) => $user->canForceDelete());

        // ── Giới hạn Moderator ──
        // Mod KHÔNG được: thay đổi role user, xóa vĩnh viễn, thay đổi settings
        Gate::define('change-user-role', fn(User $user) => $user->isAdmin());
        Gate::define('delete-user', fn(User $user) => $user->isAdmin());
    }
}
