<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Bật/tắt trạng thái theo dõi người dùng qua API.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $follower = Auth::user();
        $followingId = $request->input('user_id');

        // Không thể tự follow chính mình
        if ($follower->id == $followingId) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể theo dõi chính mình.',
            ], 400);
        }

        // Kiểm tra xem đã follow chưa
        $isFollowing = $follower->following()->where('following_id', $followingId)->exists();

        if ($isFollowing) {
            // Hủy follow
            $follower->following()->detach($followingId);
            return response()->json([
                'success' => true,
                'is_following' => false,
                'message' => 'Đã hủy theo dõi.',
            ]);
        }

        // Thực hiện follow
        $follower->following()->attach($followingId);
        
        return response()->json([
            'success' => true,
            'is_following' => true,
            'message' => 'Đã theo dõi người dùng này.',
        ]);
    }
}
