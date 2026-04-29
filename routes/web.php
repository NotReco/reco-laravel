<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ForumController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════
//  GROUP 1: PUBLIC — Accessible without login
// ═══════════════════════════════════════════════════

Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Pages
Route::view('/terms', 'pages.terms', ['title' => 'Điều khoản dịch vụ'])->name('pages.terms');
Route::view('/privacy', 'pages.privacy', ['title' => 'Chính sách bảo mật'])->name('pages.privacy');

// Phim
Route::get('/movie', [MovieController::class, 'index'])->name('explore');
Route::get('/movie/{movie}', [MovieController::class, 'show'])->name('movies.show');

// TV Shows
Route::get('/tv-shows', [\App\Http\Controllers\TvShowController::class, 'index'])->name('tv-shows.index');
Route::get('/tv-shows/{tvShow:slug}', [\App\Http\Controllers\TvShowController::class, 'show'])->name('tv-shows.show');

// Search API cho Navbar Live Search
Route::get('/api/search', function (\Illuminate\Http\Request $request) {
    $q = trim($request->query('q', ''));

    // Strip SQL wildcards
    $q = str_replace(['%', '_'], '', $q);

    // Only require minimum 2 chars
    if (mb_strlen($q) < 2) {
        return response()->json([]);
    }

    $movies = \App\Models\Movie::where(function ($qb) use ($q) {
            $qb->where('title', 'like', "%{$q}%")
               ->orWhere('original_title', 'like', "%{$q}%")
               ->orWhereHas('tags', function($qTag) use ($q) {
                   $qTag->where('name', 'like', "%{$q}%");
               });
        })
        ->select('id', 'slug', 'title', 'poster', 'release_date', 'view_count')
        ->limit(10)
        ->get()
        ->map(function ($m) {
            $m->url = route('movies.show', $m->slug);
            $m->release_year = $m->release_date ? \Carbon\Carbon::parse($m->release_date)->format('Y') : '';
            return $m;
        });

    $tvShows = \App\Models\TvShow::where(function ($qb) use ($q) {
            $qb->where('title', 'like', "%{$q}%")
               ->orWhere('original_title', 'like', "%{$q}%")
               ->orWhereHas('tags', function($qTag) use ($q) {
                   $qTag->where('name', 'like', "%{$q}%");
               });
        })
        ->select('id', 'slug', 'title', 'poster', 'first_air_date as release_date', 'view_count')
        ->limit(10)
        ->get()
        ->map(function ($t) {
            $t->url = route('tv-shows.show', $t->slug);
            $t->release_year = $t->release_date ? \Carbon\Carbon::parse($t->release_date)->format('Y') : '';
            return $t;
        });

    $results = $movies->concat($tvShows);

    // Calculate relevance score for sorting (1 is best match, 9 is worst)
    $results = $results->map(function ($item) use ($q) {
        $title = mb_strtolower(\Illuminate\Support\Str::ascii($item->title));
        $orig = mb_strtolower(\Illuminate\Support\Str::ascii($item->original_title ?? ''));
        $qLower = mb_strtolower(\Illuminate\Support\Str::ascii($q));
        
        if ($title === $qLower) {
            $score = 1;
        } elseif (str_starts_with($title, $qLower . ' ')) {
            $score = 2;
        } elseif (str_contains($title, ' ' . $qLower . ' ') || str_ends_with($title, ' ' . $qLower)) {
            $score = 3;
        } elseif (str_starts_with($title, $qLower)) {
            $score = 4;
        } elseif ($orig === $qLower) {
            $score = 5;
        } elseif (str_starts_with($orig, $qLower . ' ')) {
            $score = 6;
        } elseif (str_contains($orig, ' ' . $qLower . ' ') || str_ends_with($orig, ' ' . $qLower)) {
            $score = 7;
        } elseif (str_starts_with($orig, $qLower)) {
            $score = 8;
        } else {
            $score = 9;
        }
        
        $item->relevance_score = $score;
        return $item;
    });

    // Sort by view_count DESC first, then by relevance score ASC
    $results = $results->sortByDesc('view_count')->sortBy('relevance_score')->take(8)->values();

    // Map again to keep response small and clean
    $cleanResults = $results->map(function ($item) {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'url' => $item->url,
            'poster' => $item->poster,
            'release_year' => $item->release_year,
        ];
    });

    return response()->json($cleanResults);
})->name('api.search');

// Person
Route::get('/person', [PersonController::class, 'index'])->name('person.index');
Route::get('/person/{person}', [PersonController::class, 'show'])->name('person.show');

// Tin tức (public)
Route::get('/news', [ArticleController::class, 'index'])->name('news.index');
Route::get('/news/{article}', [ArticleController::class, 'show'])->name('news.show');

// Forum (public)
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/threads/{thread:slug}', [ForumController::class, 'show'])->name('forum.show');

// Public profiles (no auth needed)
Route::get('/users/{user}/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
Route::get('/users/{user}/favorites', [\App\Http\Controllers\ProfileController::class, 'favorites'])->name('profile.favorites');

// ═══════════════════════════════════════════════════
//  GROUP 2: AUTH — Login, Register, Password Reset
// ═══════════════════════════════════════════════════

require __DIR__ . '/auth.php';

// ═══════════════════════════════════════════════════
//  GROUP 3: AUTH + VERIFIED — Write, interact, manage
// ═══════════════════════════════════════════════════

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Reviews ──
    Route::post('/movies/{movie}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/tv-shows/{tvShow}/review', [ReviewController::class, 'storeTv'])->name('tv-shows.reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // ── Vibes ──
    Route::post('/movies/{movie}/vibe', [\App\Http\Controllers\MovieVibeController::class, 'update'])->name('movies.vibe.update');
    Route::post('/tv-shows/{tvShow}/vibe', [\App\Http\Controllers\TvShowVibeController::class, 'update'])->name('tv-shows.vibe.update');

    // ── Profile (private actions only) ──
    Route::get('/users/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/users/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/users/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Events / Quests ──
    Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
    Route::post('/events/{quest}/claim', [\App\Http\Controllers\EventController::class, 'claim'])->name('events.claim');

    // ── Favorites ──
    Route::post('/api/favorites/toggle', [\App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // ── Watchlist ──
    Route::post('/api/watchlist/toggle', [\App\Http\Controllers\WatchlistController::class, 'toggle'])->name('watchlist.toggle');
    Route::get('/my-list', [\App\Http\Controllers\WatchlistController::class, 'myList'])->name('mylist');

    // ── Follows ──
    Route::post('/api/follow/toggle', [\App\Http\Controllers\FollowController::class, 'toggle'])->name('follow.toggle');

    // ── Likes ──
    Route::post('/api/likes/toggle', [\App\Http\Controllers\LikeController::class, 'toggle'])->name('likes.toggle');

    // ── Comments ──
    Route::post('/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::post('/comments/{comment}/like', [\App\Http\Controllers\CommentController::class, 'toggleLike'])->name('comments.like');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

    // ── Article Comments ──
    Route::post('/article-comments', [ArticleCommentController::class, 'store'])->name('article-comments.store');
    Route::put('/article-comments/{comment}', [ArticleCommentController::class, 'update'])->name('article-comments.update');
    Route::post('/article-comments/{comment}/like', [ArticleCommentController::class, 'toggleLike'])->name('article-comments.like');
    Route::post('/article-comments/{comment}/report', [ArticleCommentController::class, 'report'])->name('article-comments.report');
    Route::delete('/article-comments/{comment}', [ArticleCommentController::class, 'destroy'])->name('article-comments.destroy');

    // ── Notifications ──
    Route::get('/api/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/api/notifications/turn-on', [\App\Http\Controllers\NotificationController::class, 'turnOn'])->name('notifications.turnOn');
    Route::post('/api/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/api/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/api/notifications/{id}/unread', [\App\Http\Controllers\NotificationController::class, 'markAsUnread'])->name('notifications.markAsUnread');
    Route::delete('/api/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/api/notifications/{id}/turn-off', [\App\Http\Controllers\NotificationController::class, 'turnOff'])->name('notifications.turnOff');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'all'])->name('notifications.all');

    // ── Reports (General) ──
    Route::post('/api/reports', [\App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');

    // ── Forum (auth actions) ──
    Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum/threads', [ForumController::class, 'storeThread'])->name('forum.storeThread');
    Route::get('/forum/threads/{thread:slug}/edit', [ForumController::class, 'editThread'])->name('forum.editThread');
    Route::put('/forum/threads/{thread:slug}', [ForumController::class, 'updateThread'])->name('forum.updateThread');
    Route::post('/forum/threads/{thread:slug}/reply', [ForumController::class, 'storeReply'])->name('forum.reply');
    Route::delete('/forum/threads/{thread:slug}', [ForumController::class, 'destroy'])->name('forum.destroy');
    
    Route::get('/forum/replies/{reply}/edit', [ForumController::class, 'editReply'])->name('forum.editReply');
    Route::put('/forum/replies/{reply}', [ForumController::class, 'updateReply'])->name('forum.updateReply');
    Route::delete('/forum/replies/{reply}', [ForumController::class, 'destroyReply'])->name('forum.destroyReply');

    // ── Forum Mentions API ──
    Route::get('/api/users/search', [ForumController::class, 'searchUsers'])->name('api.users.search');

    // ── Settings ──
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/security', [\App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::patch('/settings/notifications', [\App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('settings.notifications.update');
});

// ═══════════════════════════════════════════════════
//  GROUP 4: AUTH + ADMIN — Admin panel
// ═══════════════════════════════════════════════════

Route::middleware(['auth', 'can:access_admin_panel'])->prefix('staff')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Movies
    Route::middleware('can:manage_movies')->group(function () {
        Route::get('/movies', [\App\Http\Controllers\Admin\MovieController::class, 'index'])->name('movies.index');
        Route::get('/movies/{movie}/edit', [\App\Http\Controllers\Admin\MovieController::class, 'edit'])->name('movies.edit');
        Route::put('/movies/{movie}', [\App\Http\Controllers\Admin\MovieController::class, 'update'])->name('movies.update');
        Route::delete('/movies/{movie}', [\App\Http\Controllers\Admin\MovieController::class, 'destroy'])->name('movies.destroy');
    });

    // TV Shows
    Route::middleware('can:manage_tv_shows')->group(function () {
        Route::get('/tv-shows', [\App\Http\Controllers\Admin\TvShowController::class, 'index'])->name('tv-shows.index');
        Route::get('/tv-shows/{tvShow}/edit', [\App\Http\Controllers\Admin\TvShowController::class, 'edit'])->name('tv-shows.edit');
        Route::put('/tv-shows/{tvShow}', [\App\Http\Controllers\Admin\TvShowController::class, 'update'])->name('tv-shows.update');
        Route::delete('/tv-shows/{tvShow}', [\App\Http\Controllers\Admin\TvShowController::class, 'destroy'])->name('tv-shows.destroy');
    });

    // Reviews
    Route::middleware('can:manage_reviews')->group(function () {
        Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Users
    Route::middleware('can:manage_users')->group(function () {
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-ban', [\App\Http\Controllers\Admin\UserController::class, 'toggleBan'])->name('users.toggleBan');
    });

    // Roles (RBAC) (Moved to super-admin group)

    // Articles
    Route::middleware('can:manage_articles')->group(function () {
        Route::post('articles/editor-upload', [\App\Http\Controllers\Admin\ArticleEditorUploadController::class, 'store'])
            ->name('articles.editor-upload');
        Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);
    });

    // Forum Categories
    Route::resource('forum-categories', \App\Http\Controllers\Admin\ForumCategoryController::class)->except(['show'])->middleware('can:manage_forum');

    // User Titles & Avatar Frames (Managed by Super Admin or specific permission, we can just use manage_roles for now as it's sensitive, or a new permission. Let's let only Super Admin by default or define manage_users)
    Route::resource('user-titles', \App\Http\Controllers\Admin\UserTitleController::class)->except(['show'])->middleware('can:manage_roles');
    Route::resource('avatar-frames', \App\Http\Controllers\Admin\AvatarFrameController::class)->except(['show'])->middleware('can:manage_roles');

    // Quests (nhiệm vụ nhận khung/danh hiệu)
    Route::resource('quests', \App\Http\Controllers\Admin\QuestController::class)->except(['show']);

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/{report}/resolve', [\App\Http\Controllers\Admin\ReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{report}/dismiss', [\App\Http\Controllers\Admin\ReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::post('/reports/{report}/reopen', [\App\Http\Controllers\Admin\ReportController::class, 'reopen'])->name('reports.reopen');
    Route::delete('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])->name('reports.destroy');

    // Carousel
    Route::middleware('can:manage_carousel')->group(function () {
        Route::get('/carousel', [\App\Http\Controllers\Admin\CarouselController::class, 'index'])->name('carousel.index');
        Route::post('/carousel', [\App\Http\Controllers\Admin\CarouselController::class, 'store'])->name('carousel.store');
        Route::post('/carousel/auto', [\App\Http\Controllers\Admin\CarouselController::class, 'autoUpdate'])->name('carousel.autoUpdate');
        Route::post('/carousel/{movie}/up', [\App\Http\Controllers\Admin\CarouselController::class, 'moveUp'])->name('carousel.moveUp');
        Route::post('/carousel/{movie}/down', [\App\Http\Controllers\Admin\CarouselController::class, 'moveDown'])->name('carousel.moveDown');
        Route::delete('/carousel/{movie}', [\App\Http\Controllers\Admin\CarouselController::class, 'destroy'])->name('carousel.destroy');
    });
});

// ═══════════════════════════════════════════════════
//  GROUP 5: SUPER ADMIN — Core Settings & RBAC
// ═══════════════════════════════════════════════════

Route::middleware(['auth', 'can:manage_roles'])->prefix('admin')->name('super.')->group(function () {
    // Tạm thời để dashboard là chuyển hướng sang roles hoặc có dashboard riêng.
    Route::get('/', function () {
        return redirect()->route('super.roles.index');
    })->name('dashboard');

    // Roles (RBAC)
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['show']);

    // Staff & Admin account management
    Route::get('/staff-accounts', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff-accounts/create', [\App\Http\Controllers\Admin\StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff-accounts', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff-accounts/{user}/edit', [\App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff-accounts/{user}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->name('staff.update');
    Route::post('/staff-accounts/{user}/reset-password', [\App\Http\Controllers\Admin\StaffController::class, 'resetPassword'])->name('staff.resetPassword');
    Route::post('/staff-accounts/{user}/toggle-ban', [\App\Http\Controllers\Admin\StaffController::class, 'toggleBan'])->name('staff.toggleBan');
});
