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

        Auth::user()->increment('reputation_score', 1);

        try {
            if ($comment->parent_id) {
                // Notity parent comment owner
                $parentUser = $comment->parent->user;
                if ($parentUser && $parentUser->id !== Auth::id()) {
                    $parentUser->notify(new \App\Notifications\ReviewCommentNotification($comment, Auth::user()->name));
                }
            } else {
                // Notify review owner
                $reviewOwner = $comment->review->user;
                if ($reviewOwner && $reviewOwner->id !== Auth::id()) {
                    $reviewOwner->notify(new \App\Notifications\ReviewCommentNotification($comment, Auth::user()->name));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send comment notification: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            $comment->load(['user.activeFrame', 'review.comments.user']);
            $html = view('components.reviews.comment-item', ['comment' => $comment, 'review' => $comment->review])->render();
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

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
        $user = Auth::user();
        if ($comment->user_id !== $user->id && !$user->hasRole(['admin', 'moderator'])) {
            abort(403, 'Unauthorized action.');
        }

        // Parent comment will cascade delete its children due to database constraint
        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Đã xóa bình luận.');
    }

    /**
     * Toggle like for the comment.
     */
    public function toggleLike(Comment $comment)
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
}
