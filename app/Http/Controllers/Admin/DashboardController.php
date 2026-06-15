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
            'moderation_logs' => \App\Models\ActivityLog::where('action', 'like', 'moderation.%')->count(),
        ];

        $todayReviews = Review::with(['user', 'movie', 'tvShow'])
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'reviews_page')
            ->onEachSide(1);

        $todayUsers = User::whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'users_page')
            ->onEachSide(1);

        // Khởi tạo danh sách các năm
        $availableYears = Review::selectRaw('YEAR(created_at) as year')
            ->union(User::selectRaw('YEAR(created_at) as year'))
            ->pluck('year')
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();
            
        if (empty($availableYears)) {
            $availableYears = [Carbon::now()->year];
        }

        // Xác định period
        $period = request('period', 'week'); // week | month | quarter | year | 2025

        $selectedYear = null;
        if (is_numeric($period)) {
            $selectedYear = (int) $period;
        } elseif ($period === 'year') {
            $selectedYear = Carbon::now()->year;
        }

        if ($selectedYear) {
            $chartDates = collect(range(1, 12))->map(fn($m) => Carbon::create($selectedYear)->startOfYear()->addMonths($m - 1)->format('Y-m'));
            $chartLabels = $chartDates->map(fn($d) => Carbon::parse($d)->format('m/Y'))->toArray();
            $reviewsData = $chartDates->map(fn($d) =>
                Review::whereYear('created_at', substr($d, 0, 4))
                       ->whereMonth('created_at', substr($d, 5, 2))
                       ->count()
            )->toArray();
            $usersData = $chartDates->map(fn($d) =>
                User::whereYear('created_at', substr($d, 0, 4))
                    ->whereMonth('created_at', substr($d, 5, 2))
                    ->count()
            )->toArray();
            $chartTitle = 'Biến động năm ' . $selectedYear;
        } elseif ($period === 'quarter') {
            $quarterStart = Carbon::now()->firstOfQuarter()->startOfDay();
            $quarterEnd   = Carbon::now()->lastOfQuarter()->endOfDay();
            $chartDates = collect();
            $cursor = $quarterStart->copy();
            while ($cursor->lte($quarterEnd)) {
                $chartDates->push($cursor->format('Y-m-d'));
                $cursor->addWeek();
            }
            $chartLabels = $chartDates->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray();
            $reviewsData = $chartDates->map(fn($d) =>
                Review::whereBetween('created_at', [
                    Carbon::parse($d)->startOfDay(),
                    Carbon::parse($d)->endOfDay()->addDays(6)
                ])->count()
            )->toArray();
            $usersData = $chartDates->map(fn($d) =>
                User::whereBetween('created_at', [
                    Carbon::parse($d)->startOfDay(),
                    Carbon::parse($d)->endOfDay()->addDays(6)
                ])->count()
            )->toArray();
            $chartTitle = 'Biến động quý ' . Carbon::now()->quarter . '/' . Carbon::now()->year;
        } elseif ($period === 'month') {
            $chartDates = collect(range(29, 0))->map(fn($days) => today()->subDays($days)->format('Y-m-d'));
            $chartLabels = $chartDates->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray();
            $reviewsData = $chartDates->map(fn($date) => Review::whereDate('created_at', $date)->count())->toArray();
            $usersData   = $chartDates->map(fn($date) => User::whereDate('created_at', $date)->count())->toArray();
            $chartTitle = 'Biến động 30 ngày qua';
        } else {
            // week (7 ngày)
            $chartDates = collect(range(6, 0))->map(fn($days) => today()->subDays($days)->format('Y-m-d'));
            $chartLabels = $chartDates->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray();
            $reviewsData = $chartDates->map(fn($date) => Review::whereDate('created_at', $date)->count())->toArray();
            $usersData   = $chartDates->map(fn($date) => User::whereDate('created_at', $date)->count())->toArray();
            $chartTitle = 'Biến động 7 ngày qua';
        }

        return view('admin.dashboard', compact(
            'stats', 'todayReviews', 'todayUsers',
            'chartLabels', 'reviewsData', 'usersData',
            'period', 'chartTitle', 'availableYears'
        ));
    }
}
