<?php

namespace App\Http\Controllers;

use App\Models\ArticleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleCommentController extends Controller
{
    /**
     * Tạo bình luận cho bài viết (AJAX).
     */
    public function store(Request $request)
    {
        $request->validate([
            'article_id' => ['required', 'exists:articles,id'],
            'parent_id'  => ['nullable', 'exists:article_comments,id'],
            'content'    => ['required', 'string', 'max:1000'],
        ]);

        $comment = ArticleComment::create([
            'user_id'    => Auth::id(),
            'article_id' => $request->input('article_id'),
            'parent_id'  => $request->input('parent_id'),
            'content'    => $request->input('content'),
        ]);

        $comment->load('user');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id'         => $comment->id,
                    'content'    => $comment->content,
                    'parent_id'  => $comment->parent_id,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user'       => [
                        'id'     => $comment->user->id,
                        'name'   => $comment->user->name,
                        'avatar' => $comment->user->avatar,
                        'initial' => strtoupper(substr($comment->user->name, 0, 1)),
                    ],
                ],
            ]);
        }

        return back();
    }

    /**
     * Xóa bình luận (chỉ staff: admin/mod).
     */
    public function destroy(ArticleComment $comment)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->isStaff()) {
            abort(403, 'Chỉ quản trị viên mới có thể xóa bình luận.');
        }

        $comment->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Toggle like bình luận (AJAX).
     */
    public function toggleLike(ArticleComment $comment)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $like = $comment->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
            $isLiked = true;
        }

        return response()->json([
            'isLiked' => $isLiked,
            'likesCount' => $comment->likes()->count(),
        ]);
    }

    /**
     * Báo cáo bình luận (AJAX).
     */
    public function report(Request $request, ArticleComment $comment)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        // Kiểm tra user đã báo cáo comment này chưa
        $alreadyReported = $comment->reports()->where('user_id', Auth::id())->exists();
        if ($alreadyReported) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã báo cáo bình luận này rồi.',
            ], 422);
        }

        $comment->reports()->create([
            'user_id' => Auth::id(),
            'reason' => $request->input('reason'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã báo cáo bình luận. Cảm ơn bạn!',
            ]);
        }

        return back();
    }
}
