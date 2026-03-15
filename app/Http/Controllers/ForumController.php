<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Notifications\ForumReplyNotification;

class ForumController extends Controller
{
    /**
     * Trang chính diễn đàn — danh sách categories + threads gần nhất.
     */
    public function index(Request $request)
    {
        $categories = ForumCategory::active()
            ->ordered()
            ->withCount('threads')
            ->get();

        $query = ForumThread::with(['category', 'user', 'latestReply.user'])
            ->withCount('replies')
            ->recent();

        // Lọc theo category
        if ($request->filled('category')) {
            $cat = ForumCategory::where('slug', $request->category)->first();
            if ($cat) {
                $query->where('forum_category_id', $cat->id);
            }
        }

        // Tìm kiếm
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $threads = $query->paginate(15)->withQueryString();

        return view('forum.index', compact('categories', 'threads'));
    }

    /**
     * Chi tiết thread + replies.
     */
    public function show(ForumThread $thread)
    {
        $thread->incrementViews();

        $thread->load(['category', 'user']);
        $replies = $thread->replies()
            ->with('user')
            ->orderBy('created_at')
            ->paginate(20);

        return view('forum.show', compact('thread', 'replies'));
    }

    /**
     * Form tạo thread mới.
     */
    public function create()
    {
        $categories = ForumCategory::active()->ordered()->get();
        return view('forum.create', compact('categories'));
    }

    /**
     * Lưu thread mới.
     */
    public function storeThread(Request $request)
    {
        $validated = $request->validate([
            'forum_category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
        ]);

        $thread = ForumThread::create([
            'forum_category_id' => $validated['forum_category_id'],
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('forum.show', $thread)
            ->with('success', 'Bài viết đã được đăng thành công!');
    }

    /**
     * Trả lời thread.
     */
    public function storeReply(Request $request, ForumThread $thread)
    {
        if ($thread->is_locked) {
            return back()->with('error', 'Bài viết này đã bị khóa, không thể trả lời.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3',
        ]);

        $reply = $thread->replies()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        // Gửi thông báo cho chủ sở hữu thread nếu người trả lời khác với chủ thread
        if ($thread->user_id !== auth()->id()) {
            $thread->user->notify(new ForumReplyNotification($thread, auth()->user()));
        }

        // Cập nhật updated_at để thread nổi lên đầu
        $thread->touch();

        return back()->with('success', 'Trả lời đã được gửi!');
    }

    /**
     * Xóa thread (chủ sở hữu hoặc staff).
     */
    public function destroy(ForumThread $thread)
    {
        $user = auth()->user();

        if ($thread->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'Bạn không có quyền xóa bài viết này.');
        }

        $thread->delete();

        return redirect()
            ->route('forum.index')
            ->with('success', 'Bài viết đã được xóa.');
    }
}
