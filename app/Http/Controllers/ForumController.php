<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Notifications\ForumReplyNotification;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Auth;

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

        $query = ForumThread::with(['category', 'user.activeTitle', 'user.activeFrame', 'latestReply.user.activeFrame'])
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'threads' => $threads->through(function ($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'slug' => $thread->slug,
                        'url' => route('forum.show', $thread),
                        'is_pinned' => $thread->is_pinned,
                        'is_locked' => $thread->is_locked,
                        'views_count' => $thread->views_count,
                        'replies_count' => $thread->replies_count,
                        'created_at' => $thread->created_at->diffForHumans(),
                        'user' => [
                            'id' => $thread->user->id,
                            'name' => $thread->user->name,
                            'avatar' => $thread->user->avatar,
                            'active_frame' => $thread->user->activeFrame ? [
                                'image_path' => \Illuminate\Support\Facades\Storage::url($thread->user->activeFrame->image_path),
                            ] : null,
                            'initial' => strtoupper(substr($thread->user->name, 0, 1)),
                            'active_title' => $thread->user->activeTitle ? [
                                'name' => $thread->user->activeTitle->name,
                                'color_hex' => $thread->user->activeTitle->color_hex,
                            ] : null,
                        ],
                        'category' => [
                            'name' => $thread->category->name,
                        ],
                        'latest_reply' => $thread->latestReply ? [
                            'user_name' => $thread->latestReply->user->name ?? '—',
                            'created_at' => $thread->latestReply->created_at->diffForHumans(),
                        ] : null,
                    ];
                }),
                'total' => $threads->total(),
            ]);
        }

        return view('forum.index', compact('categories', 'threads'));
    }

    /**
     * Chi tiết thread + replies.
     */
    public function show(ForumThread $thread)
    {
        $thread->incrementViews();

        $thread->load(['category', 'user.activeTitle', 'user.activeFrame']);
        $replies = $thread->replies()
            ->with(['user.activeTitle', 'user.activeFrame'])
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
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => Purify::clean($validated['content']),
        ]);

        Auth::user()->increment('reputation_score', 2);

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
            'user_id' => Auth::id(),
            'content' => Purify::clean($validated['content']),
        ]);

        // Gửi thông báo cho chủ sở hữu thread nếu người trả lời khác với chủ thread
        if ($thread->user_id !== Auth::id()) {
            $thread->user->notify(new ForumReplyNotification($thread, Auth::user()));
        }

        Auth::user()->increment('reputation_score', 1);

        // Cập nhật updated_at để thread nổi lên đầu
        $thread->touch();

        return back()->with('success', 'Trả lời đã được gửi!');
    }

    /**
     * Xóa thread (chủ sở hữu hoặc staff).
     */
    public function destroy(ForumThread $thread)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($thread->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'Bạn không có quyền xóa bài viết này.');
        }

        $thread->delete();

        return redirect()
            ->route('forum.index')
            ->with('success', 'Bài viết đã được xóa.');
    }

    /**
     * Sửa thread.
     */
    public function editThread(ForumThread $thread)
    {
        if ($thread->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa bài viết này.');
        }

        $categories = ForumCategory::active()->ordered()->get();
        return view('forum.edit', compact('thread', 'categories'));
    }

    /**
     * Cập nhật thread.
     */
    public function updateThread(Request $request, ForumThread $thread)
    {
        if ($thread->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa bài viết này.');
        }

        $validated = $request->validate([
            'forum_category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
        ]);

        $thread->update([
            'forum_category_id' => $validated['forum_category_id'],
            'title' => $validated['title'],
            'content' => Purify::clean($validated['content']),
        ]);

        return redirect()->route('forum.show', $thread)->with('success', 'Bài viết đã được cập nhật.');
    }

    /**
     * Sửa Reply.
     */
    public function editReply(ForumReply $reply)
    {
        if ($reply->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa phản hồi này.');
        }

        return view('forum.edit-reply', compact('reply'));
    }

    /**
     * Cập nhật Reply.
     */
    public function updateReply(Request $request, ForumReply $reply)
    {
        if ($reply->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa phản hồi này.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3',
        ]);

        $reply->update([
            'content' => Purify::clean($validated['content']),
        ]);

        return redirect()->route('forum.show', $reply->thread)->with('success', 'Đã cập nhật phản hồi.');
    }

    /**
     * Xóa Reply (chủ hoặc staff).
     */
    public function destroyReply(ForumReply $reply)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($reply->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'Bạn không có quyền xóa phản hồi này.');
        }

        $thread = $reply->thread;
        $reply->delete();

        return redirect()->route('forum.show', $thread)->with('success', 'Đã xóa phản hồi.');
    }
}
