<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    /**
     * Hiển thị trang My List của người dùng.
     */
    public function myList()
    {
        $watchlists = Watchlist::with('movie')
            ->where('user_id', Auth::id())
            ->latest('updated_at')
            ->get()
            ->groupBy('status'); // Nhóm theo status: want_to_watch, watching, vv...

        return view('profile.watchlist', compact('watchlists'));
    }

    /**
     * Bật/Tắt phim khỏi Watchlist qua API hoặc thao tác Đổi trạng thái.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'movie_id' => ['required', 'exists:movies,id'],
            'status' => ['nullable', 'in:want_to_watch,watching,watched,dropped'],
        ]);

        $userId = Auth::id();
        $movieId = $request->movie_id;
        $status = $request->status ?? 'want_to_watch';

        $watchlist = Watchlist::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->first();

        // 1. Trường hợp đã có trong Watchlist
        if ($watchlist) {
            // Nếu gửi status mới khác status cũ -> Update
            if ($request->has('status') && $watchlist->status !== $status) {
                $watchlist->update(['status' => $status]);
                return response()->json([
                    'success' => true,
                    'in_watchlist' => true,
                    'status' => $status,
                    'message' => 'Đã cập nhật trạng thái phim.',
                ]);
            }

            // Nếu không gửi status, hoặc gửi lại status cũ -> Xóa khỏi Watchlist (Toggle OFF)
            $watchlist->delete();
            return response()->json([
                'success' => true,
                'in_watchlist' => false,
                'status' => null,
                'message' => 'Đã xóa khỏi danh sách của bạn.',
            ]);
        }

        // 2. Trường hợp chưa có -> Thêm mới (Toggle ON)
        Watchlist::create([
            'user_id' => $userId,
            'movie_id' => $movieId,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'in_watchlist' => true,
            'status' => $status,
            'message' => 'Đã thêm vào danh sách phim của bạn.',
        ]);
    }
}
