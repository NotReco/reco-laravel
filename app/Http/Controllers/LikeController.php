<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\CommentLike;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Toggle like on a review or comment.
     * Expects type (review/comment) and id.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:review,comment'],
            'id' => ['required', 'integer'],
        ]);

        $userId = Auth::id();
        $isLiked = false;

        if ($request->type === 'review') {
            $review = Review::findOrFail($request->id);
            $like = Like::where('user_id', $userId)->where('review_id', $review->id)->first();

            if ($like) {
                $like->delete();
                $review->decrement('likes_count');
            } else {
                Like::create(['user_id' => $userId, 'review_id' => $review->id]);
                $review->increment('likes_count');
                $isLiked = true;
            }

            $count = $review->likes_count;
        } else {
            $comment = Comment::findOrFail($request->id);
            $like = CommentLike::where('user_id', $userId)->where('comment_id', $comment->id)->first();

            if ($like) {
                $like->delete();
                $comment->decrement('likes_count');
            } else {
                CommentLike::create(['user_id' => $userId, 'comment_id' => $comment->id]);
                $comment->increment('likes_count');
                $isLiked = true;
            }

            $count = $comment->likes_count;
        }

        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $count
        ]);
    }
}
