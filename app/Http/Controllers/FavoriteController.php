<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Bật/tắt trạng thái yêu thích phim/TV show qua API.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'movie_id' => ['nullable', 'exists:movies,id'],
            'tv_show_id' => ['nullable', 'exists:tv_shows,id'],
        ]);

        if (!$request->movie_id && !$request->tv_show_id) {
            return response()->json(['success' => false, 'message' => 'Missing ID'], 400);
        }

        $userId = Auth::id();

        $query = Favorite::where('user_id', $userId);
        if ($request->movie_id) {
            $query->where('movie_id', $request->movie_id);
        } else {
            $query->where('tv_show_id', $request->tv_show_id);
        }

        $favorite = $query->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'is_favorited' => false,
                'message' => 'Đã gỡ khỏi danh sách Yêu thích.',
            ]);
        }

        Favorite::create([
            'user_id' => $userId,
            'movie_id' => $request->movie_id,
            'tv_show_id' => $request->tv_show_id,
        ]);

        return response()->json([
            'success' => true,
            'is_favorited' => true,
            'message' => 'Đã thêm vào danh sách Yêu thích ❤️.',
        ]);
    }
}
