<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Bật/tắt trạng thái yêu thích phim qua API.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'movie_id' => ['required', 'exists:movies,id'],
        ]);

        $userId = Auth::id();
        $movieId = $request->movie_id;

        $favorite = Favorite::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->first();

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
            'movie_id' => $movieId,
        ]);

        return response()->json([
            'success' => true,
            'is_favorited' => true,
            'message' => 'Đã thêm vào danh sách Yêu thích ❤️.',
        ]);
    }
}
