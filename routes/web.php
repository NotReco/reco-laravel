<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════
//  GROUP 1: PUBLIC — Accessible without login
// ═══════════════════════════════════════════════════

Route::get('/', [HomeController::class, 'index'])->name('home');

// Phim
Route::get('/explore', [MovieController::class, 'index'])->name('explore');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');

// Search API cho Navbar Live Search
Route::get('/api/search', function (\Illuminate\Http\Request $request) {
    $q = $request->query('q');
    if (!$q) return response()->json([]);
    $movies = \App\Models\Movie::where('title', 'like', "%{$q}%")
        ->orWhere('original_title', 'like', "%{$q}%")
        ->select('id', 'title', 'poster', 'release_date')
        ->orderByDesc('view_count')
        ->take(5)
        ->get();
    return response()->json($movies);
})->name('api.search');

// Person detail
Route::get('/person/{person}', [PersonController::class, 'show'])->name('person.show');

// TODO: Forum (public)
// Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
// Route::get('/forum/{thread}', [ForumController::class, 'show'])->name('forum.show');

// TODO: Public profile
// Route::get('/@{username}', [ProfileController::class, 'public'])->name('profile.public');

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

    // ── Profile ──
    Route::get('/users/{id}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

    // ── Notifications ──
    Route::get('/api/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/api/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/api/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'all'])->name('notifications.all');

    // TODO: Messages
    // Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

    // ── Settings ──
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
});

// ═══════════════════════════════════════════════════
//  GROUP 4: AUTH + ADMIN — Admin panel
// ═══════════════════════════════════════════════════

Route::middleware(['auth', 'role:staff'])->prefix('admin')->name('admin.')->group(function () {
    // TODO: Admin Dashboard
    // Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // TODO: Admin Movies
    // Route::resource('movies', Admin\MovieController::class);

    // TODO: Admin Reviews
    // Route::resource('reviews', Admin\ReviewController::class)->only(['index', 'show', 'update', 'destroy']);

    // TODO: Admin Users
    // Route::resource('users', Admin\UserController::class)->only(['index', 'show', 'update']);

    // TODO: Admin Categories
    // Route::resource('categories', Admin\CategoryController::class);

    // TODO: Admin Reports
    // Route::resource('reports', Admin\ReportController::class)->only(['index', 'show', 'update']);
});
