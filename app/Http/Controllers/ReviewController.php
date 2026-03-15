<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Lưu review hoặc quick rating.
     */
    public function store(Request $request, Movie $movie)
    {
        $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:10'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        // Kiểm tra user đã review phim này chưa
        $existing = Review::where('user_id', Auth::id())
            ->where('movie_id', $movie->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['rating' => 'Bạn đã đánh giá phim này rồi.']);
        }

        $isFullReview = !empty($request->input('content'));

        Review::create([
            'user_id' => Auth::id(),
            'movie_id' => $movie->id,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'excerpt' => $request->input('content') ? \Illuminate\Support\Str::limit($request->input('content'), 100) : null,
            'rating' => $request->input('rating'),
            'is_spoiler' => $request->boolean('is_spoiler'),
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', $isFullReview
            ? 'Review đã được đăng thành công! 🎬'
            : 'Đã chấm điểm thành công! ⭐');
    }

    /**
     * Cập nhật review.
     */
    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:10'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        $isFullReview = !empty($request->input('content'));

        $review->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'excerpt' => $request->input('content') ? \Illuminate\Support\Str::limit($request->input('content'), 100) : null,
            'rating' => $request->input('rating'),
            'is_spoiler' => $request->boolean('is_spoiler'),
        ]);

        return back()->with('success', 'Đã cập nhật đánh giá thành công! ✨');
    }

    /**
     * Xóa review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công! 🗑️');
    }
}
