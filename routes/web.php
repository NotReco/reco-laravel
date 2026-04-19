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
Route::get('/explore', [MovieController::class, 'index'])->name('explore');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');

// Search API cho Navbar Live Search
Route::get('/api/search', function (\Illuminate\Http\Request $request) {
    $q = trim($request->query('q', ''));

    // Strip SQL wildcards
    $q = str_replace(['%', '_'], '', $q);

    // Only allow: Unicode letters, digits 0-9, spaces (reject +, -, etc.)
    if (!$q || !preg_match('/^[\p{L}\p{N}\s]+$/u', $q)) {
        return response()->json([]);
    }

    $movies = \App\Models\Movie::where(function ($qb) use ($q) {
            $qb->where('title', 'like', "%{$q}%")
               ->orWhere('original_title', 'like', "%{$q}%");
        })
        ->select('id', 'slug', 'title', 'poster', 'release_date', 'view_count')
        ->limit(20)
        ->get();

    // Sort in PHP to ensure exact diacritics match but case-insensitive
    // mb_stripos is case-insensitive, but diacritics-sensitive!
    $movies = $movies->sortByDesc('view_count')->sortBy(function ($movie) use ($q) {
        $titleMatch = mb_stripos($movie->title, $q) !== false;
        $originalMatch = mb_stripos($movie->original_title ?? '', $q) !== false;
        return ($titleMatch || $originalMatch) ? 1 : 2;
    })->take(8)->values();

    return response()->json($movies);
})->name('api.search');

// Person detail
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
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // ── Profile (private actions only) ──
    Route::get('/users/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/users/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/users/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

    // ── Settings ──
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/security', [\App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::patch('/settings/notifications', [\App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('settings.notifications.update');
});

// ═══════════════════════════════════════════════════
//  GROUP 4: AUTH + ADMIN — Admin panel
// ═══════════════════════════════════════════════════

Route::middleware(['auth', 'role:staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Movies
    Route::get('/movies', [\App\Http\Controllers\Admin\MovieController::class, 'index'])->name('movies.index');
    Route::get('/movies/{movie}/edit', [\App\Http\Controllers\Admin\MovieController::class, 'edit'])->name('movies.edit');
    Route::put('/movies/{movie}', [\App\Http\Controllers\Admin\MovieController::class, 'update'])->name('movies.update');
    Route::delete('/movies/{movie}', [\App\Http\Controllers\Admin\MovieController::class, 'destroy'])->name('movies.destroy');

    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-ban', [\App\Http\Controllers\Admin\UserController::class, 'toggleBan'])->name('users.toggleBan');

    // Articles
    Route::post('articles/editor-upload', [\App\Http\Controllers\Admin\ArticleEditorUploadController::class, 'store'])
        ->name('articles.editor-upload');
    Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);

    // Forum Categories
    Route::resource('forum-categories', \App\Http\Controllers\Admin\ForumCategoryController::class)->except(['show']);

    // User Titles & Avatar Frames
    Route::resource('user-titles', \App\Http\Controllers\Admin\UserTitleController::class)->except(['show']);
    Route::resource('avatar-frames', \App\Http\Controllers\Admin\AvatarFrameController::class)->except(['show']);

    // Carousel
    Route::get('/carousel', [\App\Http\Controllers\Admin\CarouselController::class, 'index'])->name('carousel.index');
    Route::post('/carousel', [\App\Http\Controllers\Admin\CarouselController::class, 'store'])->name('carousel.store');
    Route::post('/carousel/auto', [\App\Http\Controllers\Admin\CarouselController::class, 'autoUpdate'])->name('carousel.autoUpdate');
    Route::post('/carousel/{movie}/up', [\App\Http\Controllers\Admin\CarouselController::class, 'moveUp'])->name('carousel.moveUp');
    Route::post('/carousel/{movie}/down', [\App\Http\Controllers\Admin\CarouselController::class, 'moveDown'])->name('carousel.moveDown');
    Route::delete('/carousel/{movie}', [\App\Http\Controllers\Admin\CarouselController::class, 'destroy'])->name('carousel.destroy');
});
