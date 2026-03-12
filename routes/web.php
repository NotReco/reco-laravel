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
    // TODO: Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    // TODO: Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // ── Profile ──
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // TODO: Favorites
    // Route::post('/api/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // TODO: Watchlist
    // Route::post('/api/watchlist/toggle', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');
    // Route::get('/my-list', [WatchlistController::class, 'myList'])->name('mylist');

    // TODO: Follows
    // Route::post('/api/follow/toggle', [FollowController::class, 'toggle'])->name('follow.toggle');

    // TODO: Likes
    // Route::post('/api/likes/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');

    // TODO: Comments
    // Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    // Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    // Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // TODO: Notifications
    // Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // TODO: Messages
    // Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

    // TODO: Settings
    // Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
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
