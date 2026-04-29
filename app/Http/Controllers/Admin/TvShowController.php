<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\TvShow;
use Illuminate\Http\Request;

class TvShowController extends Controller
{
    public function index(Request $request)
    {
        $query = TvShow::query()->with('genres')->withCount('reviews')->withAvg('reviews', 'rating');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('original_title', 'like', '%' . $request->q . '%');
            });
        }

        $tvShows = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.tv-shows.index', compact('tvShows'));
    }

    public function edit(TvShow $tvShow)
    {
        $genres = Genre::orderBy('name')->get();
        $selectedGenres = $tvShow->genres->pluck('id')->toArray();

        return view('admin.tv-shows.edit', compact('tvShow', 'genres', 'selectedGenres'));
    }

    public function update(Request $request, TvShow $tvShow)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'original_title'     => 'nullable|string|max:255',
            'tagline'            => 'nullable|string|max:500',
            'synopsis'           => 'nullable|string',
            'poster'             => 'nullable|url|max:2048',
            'backdrop'           => 'nullable|url|max:2048',
            'trailer_url'        => 'nullable|url|max:2048',
            'first_air_date'     => 'nullable|date',
            'last_air_date'      => 'nullable|date',
            'number_of_seasons'  => 'nullable|integer|min:0|max:999',
            'number_of_episodes' => 'nullable|integer|min:0|max:99999',
            'episode_runtime'    => 'nullable|integer|min:0|max:9999',
            'country'            => 'nullable|string|max:10',
            'language'           => 'nullable|string|max:10',
            'type'               => 'nullable|string|max:100',
            'tmdb_status'        => 'nullable|string|max:100',
            'status'             => 'nullable|in:active,hidden,upcoming',
            'is_approved'        => 'boolean',
            'genres'             => 'nullable|array',
            'genres.*'           => 'exists:genres,id',
        ]);

        $genres = $validated['genres'] ?? [];
        unset($validated['genres']);

        $tvShow->update($validated);
        $tvShow->genres()->sync($genres);

        return redirect()
            ->route('admin.tv-shows.index')
            ->with('success', "Đã cập nhật TV Series «{$tvShow->title}».");
    }

    public function destroy(TvShow $tvShow)
    {
        $title = $tvShow->title;
        $tvShow->delete();

        return redirect()
            ->route('admin.tv-shows.index')
            ->with('success', "Đã xóa TV Series «{$title}».");
    }
}
