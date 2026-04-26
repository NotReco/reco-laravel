<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TvShow;
use Illuminate\Http\Request;

class TvShowController extends Controller
{
    /**
     * Danh sách TV Series — paginated + search.
     */
    public function index(Request $request)
    {
        $query = TvShow::query()->withCount('reviews')->withAvg('reviews', 'rating');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $tvShows = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.tv-shows.index', compact('tvShows'));
    }

    /**
     * Form chỉnh sửa TV Series.
     */
    public function edit(TvShow $tvShow)
    {
        return view('admin.tv-shows.edit', compact('tvShow'));
    }

    /**
     * Cập nhật thông tin TV Series.
     */
    public function update(Request $request, TvShow $tvShow)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'synopsis' => 'nullable|string',
            'first_air_date' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $tvShow->update($validated);

        return redirect()
            ->route('admin.tv-shows.index')
            ->with('success', "Đã cập nhật TV Series «{$tvShow->title}».");
    }

    /**
     * Xóa TV Series.
     */
    public function destroy(TvShow $tvShow)
    {
        $title = $tvShow->title;
        $tvShow->delete();

        return redirect()
            ->route('admin.tv-shows.index')
            ->with('success', "Đã xóa TV Series «{$title}».");
    }
}
