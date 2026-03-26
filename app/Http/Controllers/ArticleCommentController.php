<?php

namespace App\Http\Controllers;

use App\Models\ArticleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleCommentController extends Controller
{
    /**
     * Tạo bình luận cho bài viết.
     */
    public function store(Request $request)
    {
        $request->validate([
            'article_id' => ['required', 'exists:articles,id'],
            'parent_id'  => ['nullable', 'exists:article_comments,id'],
            'content'    => ['required', 'string', 'max:1000'],
        ]);

        ArticleComment::create([
            'user_id'    => Auth::id(),
            'article_id' => $request->input('article_id'),
            'parent_id'  => $request->input('parent_id'),
            'content'    => $request->input('content'),
        ]);

        return back()->with('success', 'Bình luận đã được đăng.');
    }

    /**
     * Sửa bình luận.
     */
    public function update(Request $request, ArticleComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update([
            'content'   => $request->input('content'),
            'is_edited' => true,
        ]);

        return back()->with('success', 'Đã cập nhật bình luận.');
    }

    /**
     * Xóa bình luận (chủ comment hoặc staff).
     */
    public function destroy(ArticleComment $comment)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($comment->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'Unauthorized.');
        }

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }

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
     * Report a comment.
     */
    public function report(Request $request, ArticleComment $comment)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $comment->reports()->create([
            'user_id' => Auth::id(),
            'reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Đã báo cáo bình luận. Cảm ơn bạn đã đóng góp.');
    }
}
