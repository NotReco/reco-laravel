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
     * Bật/Tắt khỏi Watchlist qua API hoặc thao tác Đổi trạng thái.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'movie_id' => ['nullable', 'exists:movies,id'],
            'tv_show_id' => ['nullable', 'exists:tv_shows,id'],
            'status' => ['nullable', 'in:want_to_watch,watching,watched,dropped'],
        ]);

        if (!$request->movie_id && !$request->tv_show_id) {
            return response()->json(['success' => false, 'message' => 'Missing ID'], 400);
        }

        $userId = Auth::id();
        $status = $request->status ?? 'want_to_watch';

        $query = Watchlist::where('user_id', $userId);
        
        if ($request->movie_id) {
            $query->where('movie_id', $request->movie_id);
        } else {
            $query->where('tv_show_id', $request->tv_show_id);
        }

        $watchlist = $query->first();

        // 1. Trường hợp đã có trong Watchlist
        if ($watchlist) {
            // Nếu gửi status mới khác status cũ -> Update
            if ($request->has('status') && $watchlist->status !== $status) {
                $watchlist->update(['status' => $status]);
                
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'in_watchlist' => true,
                        'status' => $status,
                        'message' => 'Đã cập nhật trạng thái.',
                    ]);
                }
                return back()->with('success', 'Đã cập nhật trạng thái.');
            }

            // Nếu không gửi status, hoặc gửi lại status cũ -> Xóa khỏi Watchlist (Toggle OFF)
            $watchlist->delete();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'in_watchlist' => false,
                    'status' => null,
                    'message' => 'Đã xóa khỏi danh sách của bạn.',
                ]);
            }
            return back()->with('success', 'Đã xóa khỏi danh sách của bạn.');
        }

        // 2. Trường hợp chưa có -> Thêm mới (Toggle ON)
        Watchlist::create([
            'user_id' => $userId,
            'movie_id' => $request->movie_id,
            'tv_show_id' => $request->tv_show_id,
            'status' => $status,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'in_watchlist' => true,
                'status' => $status,
                'message' => 'Đã thêm vào danh sách của bạn.',
            ]);
        }
        return back()->with('success', 'Đã thêm vào danh sách của bạn.');
    }
}
