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
            ->orderBy('featured_order', 'asc')
            ->orderByDesc('updated_at')
            ->get();
            
        // Phim đủ điều kiện (có backdrop, poster, trailer) để hiển thị trong Modal thêm mới
        // Lấy 100 phim gần nhất đủ điều kiện để tối ưu tốc độ
        $eligibleMovies = Movie::with('genres')
            ->whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->whereNotNull('trailer_url')
            ->where('is_featured', false)
            ->orderByDesc('view_count')
            ->take(100)
            ->get();

        return view('admin.carousel.index', compact('featuredMovies', 'eligibleMovies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id'
        ]);

        $currentCount = Movie::where('is_featured', true)->count();
        if ($currentCount >= 20) {
            return back()->withErrors(['carousel' => 'Carousel đã đạt giới hạn 20 phim. Vui lòng gỡ bớt phim cũ.']);
        }

        $movie = Movie::findOrFail($request->movie_id);

        if (!$movie->backdrop || !$movie->poster || !$movie->trailer_url) {
            return back()->withErrors(['carousel' => 'Phim không đủ điều kiện (phải có hình nền ngang, poster, trailer).']);
        }

        $maxOrder = Movie::max('featured_order') ?? 0;

        $movie->update([
            'is_featured' => true,
            'featured_order' => $maxOrder + 1,
        ]);

        return back()->with('success', "Đã ghim phim '{$movie->title}' lên Carousel.");
    }

    public function moveUp(Movie $movie)
    {
        if (!$movie->is_featured) return back();
        $prev = Movie::where('is_featured', true)
            ->where('featured_order', '<', $movie->featured_order)
            ->orderByDesc('featured_order')
            ->first();

        if ($prev) {
            $temp = $prev->featured_order;
            $prev->update(['featured_order' => $movie->featured_order]);
            $movie->update(['featured_order' => $temp]);
        }
        return back();
    }

    public function moveDown(Movie $movie)
    {
        if (!$movie->is_featured) return back();
        $next = Movie::where('is_featured', true)
            ->where('featured_order', '>', $movie->featured_order)
            ->orderBy('featured_order', 'asc')
            ->first();

        if ($next) {
            $temp = $next->featured_order;
            $next->update(['featured_order' => $movie->featured_order]);
            $movie->update(['featured_order' => $temp]);
        }
        return back();
    }

    public function autoUpdate()
    {
        // 1. Gỡ toàn bộ phim hiện tại
        Movie::where('is_featured', true)->update([
            'is_featured' => false,
            'featured_order' => 0
        ]);

        // 2. Lấy 20 phim đáp ứng đủ điều kiện và xem nhiều nhất
        $trendingMovies = Movie::whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->whereNotNull('trailer_url')
            ->orderByDesc('view_count')
            ->take(20)
            ->get();

        // 3. Gắn lại cờ
        foreach ($trendingMovies as $index => $movie) {
            $movie->update([
                'is_featured' => true,
                'featured_order' => $index + 1,
            ]);
        }

        return back()->with('success', 'Tuyệt vời! Đã tự động càn quét và làm mới 20 phim thịnh hành nhất lên Carousel.');
    }

    public function destroy(Movie $movie)
    {
        $movie->update([
            'is_featured' => false,
            'featured_order' => 0,
        ]);

        return back()->with('success', "Đã gỡ '{$movie->title}' khỏi Carousel.");
    }
}
