<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Review;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'movies' => Movie::count(),
            'reviews' => Review::count(),
            'users' => User::count(),
            'today_reviews' => Review::whereDate('created_at', today())->count(),
        ];

        $recentReviews = Review::with(['user', 'movie'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $recentUsers = User::orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReviews', 'recentUsers'));
    }
}
