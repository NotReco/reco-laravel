<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo qua API (dùng cho dropdown).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->notifications();
        
        if ($request->query('filter') === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->take(30)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data, // [message => '', url => '']
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
                'is_new' => $notification->created_at >= now()->subDay(),
            ];
        });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function all(Request $request)
    {
        $query = Auth::user()->notifications();
        
        if ($request->query('filter') === 'unread') {
            $query->whereNull('read_at');
        }
        
        $notifications = $query->paginate(15);
        
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
     * Đánh dấu 1 thông báo là chưa đọc.
     */
    public function markAsUnread(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsUnread();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Xóa 1 thông báo.
     */
    public function destroy(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Tắt nhận loại thông báo này.
     */
    public function turnOff(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $type = $notification->type;
            
            $prefs = $user->notification_preferences ?? [];
            if (!isset($prefs['disabled_types'])) {
                $prefs['disabled_types'] = [];
            }
            
            if (!in_array($type, $prefs['disabled_types'])) {
                $prefs['disabled_types'][] = $type;
                $user->notification_preferences = $prefs;
                $user->save();
            }
            
            return response()->json(['success' => true, 'type' => $type]);
        }

        return response()->json(['success' => false], 404);
    }
    
    /**
     * Bật lại một loại thông báo.
     */
    public function turnOn(Request $request)
    {
        $request->validate([
            'type' => 'required|string'
        ]);
        
        $type = $request->input('type');
        $user = Auth::user();
        
        $prefs = $user->notification_preferences ?? [];
        if (isset($prefs['disabled_types'])) {
            $prefs['disabled_types'] = array_values(array_filter($prefs['disabled_types'], function($t) use ($type) {
                return $t !== $type;
            }));
            $user->notification_preferences = $prefs;
            $user->save();
        }
        
        return response()->json(['success' => true]);
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
