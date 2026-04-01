<?php

namespace App\Http\Controllers;

use App\Models\ArticleComment;
use App\Notifications\ArticleCommentMentioned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $comment->load(['user', 'article']);

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
        $user = Auth::user();

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
