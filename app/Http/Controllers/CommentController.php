<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'review_id' => ['required', 'exists:reviews,id'],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'review_id' => $request->input('review_id'),
            'parent_id' => $request->input('parent_id'),
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Bình luận của bạn đã được đăng thành công.');
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update([
            'content' => $request->input('content'),
            'is_edited' => true,
        ]);

        return back()->with('success', 'Bình luận đã được cập nhật.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Parent comment will cascade delete its children due to database constraint
        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}
