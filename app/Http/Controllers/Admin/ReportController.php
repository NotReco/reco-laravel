<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Danh sách báo cáo — filter by status / type.
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'reportable'])
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by reportable type (short alias)
        if ($request->filled('type')) {
            $typeMap = [
                'review'          => 'App\\Models\\Review',
                'comment'         => 'App\\Models\\Comment',
                'forum_thread'    => 'App\\Models\\ForumThread',
                'forum_reply'     => 'App\\Models\\ForumReply',
                'article_comment' => 'App\\Models\\ArticleComment',
            ];
            $morphType = $typeMap[$request->type] ?? null;
            if ($morphType) {
                $query->where('reportable_type', $morphType);
            }
        }

        // Search by reporter name or reason
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('reason', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%");
                    });
            });
        }

        $reports = $query->paginate(20)->withQueryString();

        $pendingCount = Report::where('status', 'pending')->count();

        return view('admin.reports.index', compact('reports', 'pendingCount'));
    }

    /**
     * Đánh dấu báo cáo là đã xử lý (resolved).
     */
    public function resolve(Report $report)
    {
        $report->update(['status' => 'resolved']);

        return back()->with('success', 'Báo cáo đã được đánh dấu là đã xử lý.');
    }

    /**
     * Bác bỏ báo cáo (dismissed — không có vấn đề).
     */
    public function dismiss(Report $report)
    {
        $report->update(['status' => 'dismissed']);

        return back()->with('success', 'Báo cáo đã bị bác bỏ.');
    }

    /**
     * Đặt lại trạng thái về pending.
     */
    public function reopen(Report $report)
    {
        $report->update(['status' => 'pending']);

        return back()->with('success', 'Báo cáo đã được mở lại.');
    }

    /**
     * Xóa báo cáo.
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return back()->with('success', 'Báo cáo đã bị xóa.');
    }
}
