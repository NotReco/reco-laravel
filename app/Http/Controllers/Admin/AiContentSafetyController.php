<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ArticleComment;
use App\Models\Comment;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Report;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AiContentSafetyController extends Controller
{
    protected $typeMap = [
        'review'          => Review::class,
        'comment'         => Comment::class,
        'article_comment' => ArticleComment::class,
        'forum_thread'    => ForumThread::class,
        'forum_reply'     => ForumReply::class,
    ];

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'ai_logs');

        $aiLogs = null;
        $reports = null;
        $hiddenContent = null;

        if ($tab === 'ai_logs') {
            $paginator = ActivityLog::with('user')
                ->where('action', 'like', 'moderation.%')
                ->orWhereIn('action', ['ai_assistant.adult_violation', 'ai_assistant.adult_muted'])
                ->latest()
                ->paginate(20)
                ->withQueryString();
            $aiLogs = $this->formatAiLogs($paginator);
        } elseif ($tab === 'reports') {
            $paginator = Report::with(['user', 'reportable'])
                ->where('status', 'pending')
                ->latest()
                ->paginate(20)
                ->withQueryString();
            $reports = $this->formatReports($paginator);
        } elseif ($tab === 'hidden_content') {
            $paginator = $this->getHiddenContent();
            $hiddenContent = $this->formatHiddenContent($paginator);
        }

        return view('admin.ai_content_safety.index', compact('tab', 'aiLogs', 'reports', 'hiddenContent'));
    }

    /**
     * Resolve a report
     */
    public function resolveReport(Report $report)
    {
        $report->update(['status' => 'resolved']);
        $this->logAdminAction('ai_safety.report.resolved', get_class($report), $report->id, "Đã duyệt báo cáo (ID: {$report->id})");
        return back()->with('success', 'Đã đánh dấu báo cáo là đã xử lý.');
    }

    /**
     * Dismiss a report
     */
    public function dismissReport(Report $report)
    {
        $report->update(['status' => 'dismissed']);
        $this->logAdminAction('ai_safety.report.dismissed', get_class($report), $report->id, "Đã bỏ qua báo cáo (ID: {$report->id})");
        return back()->with('success', 'Đã bỏ qua báo cáo này.');
    }

    /**
     * Hide target (Prioritize status, fallback to soft delete)
     */
    public function hideTarget($type, $id)
    {
        $modelClass = $this->typeMap[$type] ?? null;
        if (!$modelClass) {
            return back()->with('error', 'Loại nội dung không hợp lệ.');
        }

        $target = $modelClass::find($id);
        if (!$target) {
            return back()->with('error', 'Nội dung này không còn tồn tại.');
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn((new $modelClass)->getTable(), 'status')) {
            $target->update(['status' => 'hidden']);
            $this->logAdminAction('ai_safety.target.hidden', $modelClass, $id, "Đã ẩn nội dung (status=hidden).");
            return back()->with('success', 'Đã ẩn nội dung (đổi trạng thái).');
        }

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass))) {
            $target->delete();
            $this->logAdminAction('ai_safety.target.deleted', $modelClass, $id, "Đã xóa tạm nội dung (soft delete).");
            return back()->with('success', 'Đã ẩn nội dung (Soft Delete).');
        }

        return back()->with('error', 'Model này không hỗ trợ ẩn hoặc xóa tạm.');
    }

    /**
     * Delete target (Soft delete)
     */
    public function deleteTarget($type, $id)
    {
        $modelClass = $this->typeMap[$type] ?? null;
        if (!$modelClass) {
            return back()->with('error', 'Loại nội dung không hợp lệ.');
        }

        $target = $modelClass::find($id);
        if (!$target) {
            return back()->with('error', 'Nội dung này không còn tồn tại.');
        }

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass))) {
            $target->delete();
            $this->logAdminAction('ai_safety.target.deleted', $modelClass, $id, "Đã xóa tạm nội dung (soft delete).");
            return back()->with('success', 'Đã xóa nội dung (Soft Delete).');
        }

        return back()->with('error', 'Model này không hỗ trợ xóa tạm (Soft Delete).');
    }

    /**
     * Admin Activity Log Helper
     */
    protected function logAdminAction($action, $targetType = null, $targetId = null, $desc = '')
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'description' => \Illuminate\Support\Str::limit($desc, 120),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to log Ai Safety action: " . $e->getMessage());
        }
    }

    /**
     * Helper to get hidden/trashed content
     */
    protected function getHiddenContent()
    {
        $collection = collect();

        // 1. Reviews (status = hidden)
        $reviews = Review::where('status', 'hidden')->with('user')->get()->map(function ($item) {
            $item->safety_type = 'review';
            $item->safety_date = $item->updated_at;
            return $item;
        });
        $collection = $collection->concat($reviews);

        // Models with SoftDeletes
        $softDeleteModels = [
            'comment' => Comment::class,
            'article_comment' => ArticleComment::class,
            'forum_thread' => ForumThread::class,
            'forum_reply' => ForumReply::class,
        ];

        foreach ($softDeleteModels as $type => $modelClass) {
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass))) {
                $trashed = $modelClass::onlyTrashed()->with('user')->get()->map(function ($item) use ($type) {
                    $item->safety_type = $type;
                    $item->safety_date = $item->deleted_at;
                    return $item;
                });
                $collection = $collection->concat($trashed);
            }
        }

        $sorted = $collection->sortByDesc('safety_date')->values();
        $page = request()->get('page', 1);
        $perPage = 20;

        return new LengthAwarePaginator(
            $sorted->forPage($page, $perPage),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    // ── Formatting Helpers ──

    protected function formatAiLogs($paginator)
    {
        $paginator->getCollection()->transform(function ($log) {
            // Parse description to extract metadata like source, severity, etc.
            // Format: Nguồn: RULE | Phân loại: [hate] | Mức độ: high | Hành động: delete | Tự tin: 1.0 | Từ khóa: [abc]\nNội dung: ...
            $source = 'system';
            $severity = 'null';
            $matchedWords = '';
            
            if (preg_match('/Nguồn:\s*(.*?)\s*\|/', $log->description, $m)) $source = strtolower(trim($m[1]));
            if (preg_match('/Mức độ:\s*(.*?)\s*\|/', $log->description, $m)) $severity = strtolower(trim($m[1]));
            if (preg_match('/Từ khóa:\s*\[(.*?)\]/', $log->description, $m)) $matchedWords = trim($m[1]);
            
            $excerpt = $log->description;
            if (preg_match('/Nội dung:\s*(.*)/s', $log->description, $m)) {
                $excerpt = trim($m[1]);
            }

            if (str_starts_with($log->action, 'ai_assistant.')) {
                $source = 'ai_assistant';
                $severity = str_contains($log->action, 'muted') ? 'high' : 'medium';
            }

            return [
                'id' => $log->id,
                'type' => 'activity_log',
                'label' => 'Nhật ký chặn',
                'user_name' => $log->user ? $log->user->name : 'Khách',
                'excerpt' => $excerpt,
                'source' => $source,
                'severity' => $severity,
                'matched_words' => $matchedWords,
                'status' => $log->action, // reuse status field for action
                'created_at' => $log->created_at,
                'target_type' => null,
                'target_id' => null,
                'actions' => ['view_details'],
                'raw_description' => $log->description,
                'ip_address' => $log->ip_address,
            ];
        });
        return $paginator;
    }

    protected function formatReports($paginator)
    {
        $paginator->getCollection()->transform(function ($report) {
            $targetType = class_basename($report->reportable_type);
            $targetModelType = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $targetType));
            
            $excerpt = 'Không còn tồn tại';
            $targetId = null;
            if ($report->reportable) {
                $targetId = $report->reportable->id;
                $excerpt = $report->reportable->content ?? $report->reportable->title ?? 'N/A';
            }

            return [
                'id' => $report->id,
                'type' => 'report',
                'label' => 'Báo cáo',
                'user_name' => $report->user ? $report->user->name : 'Khách',
                'excerpt' => $excerpt,
                'source' => 'report',
                'severity' => 'medium', // Reports are implicitly medium/high
                'status' => $report->status,
                'created_at' => $report->created_at,
                'target_type' => $targetModelType,
                'target_id' => $targetId,
                'reason' => $report->reason,
                'actions' => ['resolve', 'dismiss', 'hide_target', 'delete_target'],
            ];
        });
        return $paginator;
    }

    protected function formatHiddenContent($paginator)
    {
        $paginator->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'type' => $item->safety_type,
                'label' => str_replace('_', ' ', strtoupper($item->safety_type)),
                'user_name' => $item->user ? $item->user->name : 'Khách',
                'excerpt' => $item->content ?? $item->title ?? 'N/A',
                'source' => 'system',
                'severity' => 'null',
                'status' => (isset($item->status) && $item->status === 'hidden') ? 'hidden' : 'trashed',
                'created_at' => $item->safety_date,
                'target_type' => $item->safety_type,
                'target_id' => $item->id,
                'actions' => [], // Add restore action later if needed
            ];
        });
        return $paginator;
    }
}
