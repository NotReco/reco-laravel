<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Lưu review hoặc quick rating cho Movie.
     */
    public function store(Request $request, Movie $movie)
    {
        return $this->handleStore($request, $movie->id, null);
    }

    /**
     * Lưu review hoặc quick rating cho TvShow.
     */
    public function storeTv(Request $request, \App\Models\TvShow $tvShow)
    {
        return $this->handleStore($request, null, $tvShow->id);
    }

    protected function handleStore(Request $request, ?int $movieId, ?int $tvShowId)
    {
        $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:10'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        $query = Review::where('user_id', Auth::id());
        if ($movieId) $query->where('movie_id', $movieId);
        if ($tvShowId) $query->where('tv_show_id', $tvShowId);
        
        $existing = $query->first();

        if ($existing) {
            return back()->withErrors(['rating' => 'Bạn đã đánh giá nội dung này rồi.']);
        }

        $isFullReview = !empty($request->input('content'));

        Review::create([
            'user_id' => Auth::id(),
            'movie_id' => $movieId,
            'tv_show_id' => $tvShowId,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'excerpt' => $request->input('content') ? \Illuminate\Support\Str::limit($request->input('content'), 100) : null,
            'rating' => $request->input('rating'),
            'is_spoiler' => $request->boolean('is_spoiler'),
            'status' => 'published',
            'published_at' => now(),
        ]);

        $score = $isFullReview ? 5 : 1;
        Auth::user()->increment('reputation_score', $score);
        
        if ($movieId) {
            $model = Movie::find($movieId);
            $route = route('movies.show', $model);
        } else {
            $model = \App\Models\TvShow::find($tvShowId);
            $route = route('tv-shows.show', $model);
        }

        return redirect($route)->with('success', $isFullReview
            ? 'Review đã được đăng thành công!'
            : 'Đã chấm điểm thành công!');
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
