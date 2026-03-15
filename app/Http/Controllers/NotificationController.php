<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo qua API (dùng cho dropdown).
     */
    public function index()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()->take(10)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data, // [message => '', url => '']
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Xem tất cả thông báo (Trang riêng)
     */
    public function all()
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        
        return view('profile.notifications', compact('notifications'));
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Đánh dấu TẤT CẢ thông báo là đã đọc.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
