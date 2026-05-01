<?php

namespace App\Http\Controllers;

use App\Models\ArticleComment;
use App\Notifications\ArticleCommentMentioned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        $parentId = $request->input('parent_id');
        if ($parentId) {
            $parentComment = ArticleComment::query()
                ->select('id', 'article_id', 'parent_id')
                ->with(['parent:id,parent_id'])
                ->find($parentId);

            if (!$parentComment || (int) $parentComment->article_id !== (int) $request->input('article_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bình luận cha không hợp lệ hoặc không thuộc bài viết này.',
                ], 422);
            }

            // Cho phép tối đa 2 tầng dưới bình luận gốc (root -> reply -> nested reply).
            if ($parentComment->parent_id && optional($parentComment->parent)->parent_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ hỗ trợ tối đa 2 cấp phản hồi cho mỗi bình luận gốc.',
                ], 422);
            }
        }

        $comment = ArticleComment::create([
            'user_id'    => Auth::id(),
            'article_id' => $request->input('article_id'),
            'parent_id'  => $parentId,
            'content'    => $request->input('content'),
        ]);

        Auth::user()->increment('reputation_score', 1);

        $comment->load(['user', 'user.activeFrame', 'article', 'parent']);

        // Xử lý gửi thông báo khi có @tên (mention)
        $content = $comment->content;
        if (str_contains($content, '@')) {
            // Lấy danh sách user tham gia bình luận trong bài + tác giả
            $participantIds = \App\Models\ArticleComment::where('article_id', $comment->article_id)
                ->distinct()
                ->pluck('user_id');
            
            $articleUserId = $comment->article->user_id ?? null;
            if ($articleUserId) {
                $participantIds->push($articleUserId);
            }

            $users = \App\Models\User::whereIn('id', $participantIds->filter()->unique())->get();
            $mentionerName = $comment->user->name;

            foreach ($users as $notifiableUser) {
                if ($notifiableUser->id === \Illuminate\Support\Facades\Auth::id()) {
                    continue; // Không thông báo cho chính mình
                }
                
                // Match đúng @username trọn vẹn, tránh khớp nhầm khi sau tên còn chữ/số/_.
                $pattern = '/@' . preg_quote($notifiableUser->name, '/') . '(?![\p{L}\p{N}_])/u';
                
                if (preg_match($pattern, $content)) {
                    try {
                        $notifiableUser->notify(new ArticleCommentMentioned($comment, $mentionerName));
                    } catch (\Throwable $e) {
                        // Không để lỗi notification làm hỏng luồng tạo bình luận/trả lời.
                        Log::warning('Failed to send article comment mention notification', [
                            'comment_id' => $comment->id,
                            'notifiable_user_id' => $notifiableUser->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id'         => $comment->id,
                    'uuid'       => $comment->uuid,
                    'content'    => $comment->content,
                    'parent_id'  => $comment->parent_id,
                    'parent_uuid' => $comment->parent ? $comment->parent->uuid : null,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user'       => [
                        'id'     => $comment->user->id,
                        'name'   => $comment->user->name,
                        'avatar' => $comment->user->avatar,
                        'initial' => strtoupper(substr($comment->user->name, 0, 1)),
                        'slug'   => $comment->user->slug,
                        'active_frame' => $comment->user->activeFrame ? [
                            'image_path' => Storage::url($comment->user->activeFrame->image_path),
                        ] : null,
                    ],
                ],
            ]);
        }

        return back();
    }

    /**
     * Cập nhật bình luận (chủ bình luận).
     */
    public function update(Request $request, ArticleComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền sửa bình luận này.',
            ], 403);
        }

        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update([
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật bình luận.',
            'content' => $comment->content,
        ]);
    }

    /**
     * Xóa bình luận (chủ bình luận hoặc staff).
     */
    public function destroy(ArticleComment $comment)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($comment->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
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
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['boolean'],
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
            'description' => $request->input('description'),
            'is_public' => $request->boolean('is_public'),
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
