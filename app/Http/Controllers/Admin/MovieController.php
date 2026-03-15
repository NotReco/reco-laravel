<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Danh sách phim — paginated + search.
     */
    public function index(Request $request)
    {
        $query = Movie::query()->withCount('reviews')->withAvg('reviews', 'rating');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $movies = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.movies.index', compact('movies'));
    }

    /**
     * Form chỉnh sửa phim.
     */
    public function edit(Movie $movie)
    {
        return view('admin.movies.edit', compact('movie'));
    }

    /**
     * Cập nhật thông tin phim.
     */
    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'overview' => 'nullable|string',
            'release_date' => 'nullable|date',
            'runtime' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
        ]);

        $movie->update($validated);

        return redirect()
            ->route('admin.movies.index')
            ->with('success', "Đã cập nhật phim «{$movie->title}».");
    }

    /**
     * Xóa phim.
     */
    public function destroy(Movie $movie)
    {
        $title = $movie->title;
        $movie->delete();

        return redirect()
            ->route('admin.movies.index')
            ->with('success', "Đã xóa phim «{$title}».");
    }
}
