<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $query = Movie::query()->with('genres')->withCount('reviews')->withAvg('reviews', 'rating');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('original_title', 'like', '%' . $request->q . '%');
            });
        }

        $movies = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.movies.index', compact('movies'));
    }

    public function edit(Movie $movie)
    {
        $genres = Genre::orderBy('name')->get();
        $selectedGenres = $movie->genres->pluck('id')->toArray();

        return view('admin.movies.edit', compact('movie', 'genres', 'selectedGenres'));
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'original_title' => 'nullable|string|max:255',
            'tagline'        => 'nullable|string|max:500',
            'synopsis'       => 'nullable|string',
            'poster'         => 'nullable|url|max:2048',
            'backdrop'       => 'nullable|url|max:2048',
            'trailer_url'    => 'nullable|url|max:2048',
            'release_date'   => 'nullable|date',
            'runtime'        => 'nullable|integer|min:0|max:9999',
            'country'        => 'nullable|string|max:10',
            'language'       => 'nullable|string|max:10',
            'budget'         => 'nullable|integer|min:0',
            'revenue'        => 'nullable|integer|min:0',
            'status'         => 'nullable|in:active,hidden,upcoming',
            'is_approved'    => 'boolean',
            'genres'         => 'nullable|array',
            'genres.*'       => 'exists:genres,id',
        ]);

        // Tách genres ra khỏi validated để sync riêng
        $genres = $validated['genres'] ?? [];
        unset($validated['genres']);

        $movie->update($validated);
        $movie->genres()->sync($genres);

        return redirect()
            ->route('admin.movies.index')
            ->with('success', "Đã cập nhật phim «{$movie->title}».");
    }

    public function destroy(Movie $movie)
    {
        $title = $movie->title;
        $movie->delete();

        return redirect()
            ->route('admin.movies.index')
            ->with('success', "Đã xóa phim «{$title}».");
    }
}
