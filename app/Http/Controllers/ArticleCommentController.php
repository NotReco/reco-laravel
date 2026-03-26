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
        if ($comment->user_id !== Auth::id() && !auth()->user()->isStaff()) {
            abort(403, 'Unauthorized.');
        }

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}
