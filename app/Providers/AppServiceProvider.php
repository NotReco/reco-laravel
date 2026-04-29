<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Listeners\BroadcastNotification;
use App\Models\User;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;

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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Paginator::defaultView('vendor.pagination.reco');

        $this->defineGates();

        // Broadcast mọi database notification qua Reverb WebSocket
        Event::listen(NotificationSent::class, BroadcastNotification::class);

        Relation::morphMap([
            'ArticleComment' => 'App\Models\ArticleComment',
            'Comment'        => 'App\Models\Comment',
            'ForumReply'     => 'App\Models\ForumReply',
            'Review'         => 'App\Models\Review',
        ]);

        // ── Quest Observers ──
        \App\Models\Review::observe(\App\Observers\ReviewObserver::class);
        \App\Models\Comment::observe(\App\Observers\CommentObserver::class);
        \App\Models\Like::observe(\App\Observers\LikeObserver::class);
        \App\Models\Follow::observe(\App\Observers\FollowObserver::class);
        \App\Models\ForumThread::observe(\App\Observers\ForumThreadObserver::class);
        \App\Models\ForumReply::observe(\App\Observers\ForumReplyObserver::class);
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
