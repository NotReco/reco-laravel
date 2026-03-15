<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Danh sách cuộc hội thoại.
     */
    public function index()
    {
        $userId = Auth::id();

        // Lấy danh sách conversations — nhóm theo partner
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($msg) use ($userId) {
                return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            })
            ->unique()
            ->values();

        // Load partner info + last message + unread count
        $partners = $conversations->map(function ($partnerId) use ($userId) {
            $partner = User::find($partnerId);
            if (!$partner) return null;

            $lastMessage = Message::between($userId, $partnerId)
                ->orderByDesc('created_at')
                ->first();

            $unreadCount = Message::where('sender_id', $partnerId)
                ->where('receiver_id', $userId)
                ->unread()
                ->count();

            return (object) [
                'user' => $partner,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ];
        })->filter();

        return view('messages.index', compact('partners'));
    }

    /**
     * Chat với user cụ thể.
     */
    public function show($userId)
    {
        $partner = User::findOrFail($userId);
        $currentUserId = Auth::id();

        if ($partner->id === $currentUserId) {
            return redirect()->route('messages.index');
        }

        // Đánh dấu đã đọc tin nhắn từ partner
        Message::where('sender_id', $partner->id)
            ->where('receiver_id', $currentUserId)
            ->unread()
            ->update(['read_at' => now()]);

        // Lấy messages giữa 2 user
        $messages = Message::between($currentUserId, $partner->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();

        // Danh sách conversations cho sidebar
        $conversations = Message::where('sender_id', $currentUserId)
            ->orWhere('receiver_id', $currentUserId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($msg) use ($currentUserId) {
                return $msg->sender_id === $currentUserId ? $msg->receiver_id : $msg->sender_id;
            })
            ->unique()
            ->values();

        $partners = $conversations->map(function ($partnerId) use ($currentUserId) {
            $p = User::find($partnerId);
            if (!$p) return null;
            $lastMessage = Message::between($currentUserId, $partnerId)->orderByDesc('created_at')->first();
            $unreadCount = Message::where('sender_id', $partnerId)->where('receiver_id', $currentUserId)->unread()->count();
            return (object) ['user' => $p, 'last_message' => $lastMessage, 'unread_count' => $unreadCount];
        })->filter();

        return view('messages.index', compact('partners', 'partner', 'messages'));
    }

    /**
     * Gửi tin nhắn.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('messages.show', $validated['receiver_id'])
            ->with('success', 'Tin nhắn đã gửi!');
    }
}
