<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use App\Models\ForumThread;
use App\Models\TvShow;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'movies'          => Movie::count(),
            'tv_shows'        => TvShow::count(),
            'reviews'         => Review::count(),
            'users'           => User::count(),
            'today_reviews'   => Review::whereDate('created_at', today())->count(),
            'forum_threads'   => ForumThread::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
        ];

        $todayReviews = Review::with(['user', 'movie'])
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'reviews_page');

        $todayUsers = User::whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'users_page');

        // Dữ liệu biểu đồ 7 ngày qua
        $chartDates = collect(range(6, 0))->map(fn($days) => today()->subDays($days)->format('Y-m-d'));
        
        $reviewsData = [];
        $usersData = [];

        foreach ($chartDates as $date) {
            $reviewsData[] = Review::whereDate('created_at', $date)->count();
            $usersData[] = User::whereDate('created_at', $date)->count();
        }

        // Định dạng nhãn ngày (VD: 25/04)
        $chartLabels = $chartDates->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray();

        return view('admin.dashboard', compact('stats', 'todayReviews', 'todayUsers', 'chartLabels', 'reviewsData', 'usersData'));
    }
}
