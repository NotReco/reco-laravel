<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class CarouselController extends Controller
{
    public function index()
    {
        $featuredMovies = Movie::with('genres')
            ->where('is_featured', true)
            ->get()
            ->map(function($m) { $m->media_type = 'movie'; return $m; });

        $featuredTvShows = \App\Models\TvShow::with('genres')
            ->where('is_featured', true)
            ->get()
            ->map(function($t) { $t->media_type = 'tv'; return $t; });

        $featuredMovies = $featuredMovies->concat($featuredTvShows)
            ->sortBy('featured_order')
            ->values();
            
        // Tất cả phim & TV đủ điều kiện (kể cả đã ghim) — modal sẽ cho biết đã ghim chưa
        $eligibleMovies = Movie::with('genres')
            ->whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->whereNotNull('trailer_url')
            ->orderByDesc('view_count')
            ->take(50)
            ->get()
            ->map(function($m) { $m->media_type = 'movie'; return $m; });

        $eligibleTvShows = \App\Models\TvShow::with('genres')
            ->whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->whereNotNull('trailer_url')
            ->orderByDesc('view_count')
            ->take(50)
            ->get()
            ->map(function($t) { $t->media_type = 'tv'; return $t; });

        $eligibleMovies = $eligibleMovies->concat($eligibleTvShows)
            ->sortByDesc('view_count')
            ->values();

        return view('admin.carousel.index', compact('featuredMovies', 'eligibleMovies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'media_id' => 'required',
            'media_type' => 'required|in:movie,tv'
        ]);

        $currentCount = Movie::where('is_featured', true)->count() + \App\Models\TvShow::where('is_featured', true)->count();
        if ($currentCount >= 10) {
            return back()->withErrors(['carousel' => 'Carousel đã đạt giới hạn 10 nội dung. Vui lòng gỡ bớt trước.']);
        }

        $media = $request->media_type === 'movie' ? Movie::findOrFail($request->media_id) : \App\Models\TvShow::findOrFail($request->media_id);

        if (!$media->backdrop || !$media->poster || !$media->trailer_url) {
            return back()->withErrors(['carousel' => 'Phim không đủ điều kiện (phải có hình nền ngang, poster, trailer).']);
        }

        $maxMovieOrder = Movie::max('featured_order') ?? 0;
        $maxTvOrder = \App\Models\TvShow::max('featured_order') ?? 0;
        $maxOrder = max($maxMovieOrder, $maxTvOrder);

        $media->update([
            'is_featured' => true,
            'featured_order' => $maxOrder + 1,
        ]);

        return back()->with('success', "Đã ghim '{$media->title}' lên Carousel.");
    }

    public function moveUp($type, $id)
    {
        $media = $type === 'movie' ? Movie::find($id) : \App\Models\TvShow::find($id);
        if (!$media || !$media->is_featured) return back();

        $featuredMovies = Movie::where('is_featured', true)->get()->map(function($m) { $m->media_type = 'movie'; return $m; });
        $featuredTvShows = \App\Models\TvShow::where('is_featured', true)->get()->map(function($t) { $t->media_type = 'tv'; return $t; });
        $allFeatured = $featuredMovies->concat($featuredTvShows)->sortByDesc('featured_order')->values();

        $prev = $allFeatured->where('featured_order', '<', $media->featured_order)->first();

        if ($prev) {
            $temp = $prev->featured_order;
            $prevType = $prev->media_type === 'movie' ? Movie::find($prev->id) : \App\Models\TvShow::find($prev->id);
            
            $prevType->update(['featured_order' => $media->featured_order]);
            $media->update(['featured_order' => $temp]);
        }
        return back();
    }

    public function moveDown($type, $id)
    {
        $media = $type === 'movie' ? Movie::find($id) : \App\Models\TvShow::find($id);
        if (!$media || !$media->is_featured) return back();

        $featuredMovies = Movie::where('is_featured', true)->get()->map(function($m) { $m->media_type = 'movie'; return $m; });
        $featuredTvShows = \App\Models\TvShow::where('is_featured', true)->get()->map(function($t) { $t->media_type = 'tv'; return $t; });
        $allFeatured = $featuredMovies->concat($featuredTvShows)->sortBy('featured_order')->values();

        $next = $allFeatured->where('featured_order', '>', $media->featured_order)->first();

        if ($next) {
            $temp = $next->featured_order;
            $nextType = $next->media_type === 'movie' ? Movie::find($next->id) : \App\Models\TvShow::find($next->id);
            
            $nextType->update(['featured_order' => $media->featured_order]);
            $media->update(['featured_order' => $temp]);
        }
        return back();
    }

    public function autoUpdate()
    {
        Movie::where('is_featured', true)->update([
            'is_featured' => false,
            'featured_order' => 0
        ]);
        \App\Models\TvShow::where('is_featured', true)->update([
            'is_featured' => false,
            'featured_order' => 0
        ]);

        $trendingMovies = Movie::whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->whereNotNull('trailer_url')
            ->orderByDesc('view_count')
            ->take(5)
            ->get();
            
        $trendingTvShows = \App\Models\TvShow::whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->whereNotNull('trailer_url')
            ->orderByDesc('view_count')
            ->take(5)
            ->get();
            
        $trending = $trendingMovies->concat($trendingTvShows)->sortByDesc('view_count')->values();

        foreach ($trending as $index => $media) {
            $media->update([
                'is_featured' => true,
                'featured_order' => $index + 1,
            ]);
        }

        return back()->with('success', 'Đã tự động làm mới top 10 nội dung thịnh hành nhất lên Carousel (5 Phim + 5 TV Series).');
    }

    public function destroy($type, $id)
    {
        $media = $type === 'movie' ? Movie::find($id) : \App\Models\TvShow::find($id);
        if ($media) {
            $media->update([
                'is_featured' => false,
                'featured_order' => 0,
            ]);
            return back()->with('success', "Đã gỡ '{$media->title}' khỏi Carousel.");
        }
        return back();
    }

    /**
     * AJAX endpoint: di chuyển lên/xuống không reload trang.
     * POST /carousel/{type}/{id}/move   { direction: 'up'|'down' }
     * Returns: JSON { ok: true, items: [...] }
     */
    public function moveAjax(Request $request, $type, $id)
    {
        $media = $type === 'movie' ? Movie::find($id) : \App\Models\TvShow::find($id);
        if (!$media || !$media->is_featured) {
            return response()->json(['ok' => false, 'message' => 'Not found'], 404);
        }

        $direction = $request->input('direction', 'up');

        $allFeatured = Movie::where('is_featured', true)->get()->map(fn($m) => $m->setAttribute('media_type', 'movie'))
            ->concat(\App\Models\TvShow::where('is_featured', true)->get()->map(fn($t) => $t->setAttribute('media_type', 'tv')))
            ->sortBy('featured_order')
            ->values();

        if ($direction === 'up') {
            $other = $allFeatured->where('featured_order', '<', $media->featured_order)->sortByDesc('featured_order')->first();
        } else {
            $other = $allFeatured->where('featured_order', '>', $media->featured_order)->sortBy('featured_order')->first();
        }

        if ($other) {
            $temp = $other->featured_order;
            $otherModel = $other->media_type === 'movie' ? Movie::find($other->id) : \App\Models\TvShow::find($other->id);
            $otherModel->update(['featured_order' => $media->featured_order]);
            $media->update(['featured_order' => $temp]);
        }

        // Trả về toàn bộ danh sách đã sắp xếp mới
        $items = Movie::where('is_featured', true)->get()->map(fn($m) => [
                'id'         => $m->id,
                'type'       => 'movie',
                'title'      => $m->title,
                'poster'     => $m->poster,
                'media_type' => 'movie',
                'order'      => $m->featured_order,
                'genres'     => $m->genres()->pluck('name')->take(2)->values(),
                'genres_extra' => max(0, $m->genres()->count() - 2),
                'move_url'   => route('admin.carousel.moveAjax', ['type' => 'movie', 'id' => $m->id]),
                'destroy_url'=> route('admin.carousel.destroy', ['type' => 'movie', 'id' => $m->id]),
            ])
            ->concat(
                \App\Models\TvShow::where('is_featured', true)->get()->map(fn($t) => [
                    'id'         => $t->id,
                    'type'       => 'tv',
                    'title'      => $t->title,
                    'poster'     => $t->poster,
                    'media_type' => 'tv',
                    'order'      => $t->featured_order,
                    'genres'     => $t->genres()->pluck('name')->take(2)->values(),
                    'genres_extra' => max(0, $t->genres()->count() - 2),
                    'move_url'   => route('admin.carousel.moveAjax', ['type' => 'tv', 'id' => $t->id]),
                    'destroy_url'=> route('admin.carousel.destroy', ['type' => 'tv', 'id' => $t->id]),
                ])
            )
            ->sortBy('order')
            ->values();

        return response()->json(['ok' => true, 'items' => $items]);
    }
}
