<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Review;
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
     * Cộng +5 uy tín cho người báo cáo.
     * Nếu báo cáo liên quan đến Review, tự động ẩn review đó.
     */
    public function resolve(Report $report)
    {
        $report->update(['status' => 'resolved']);

        // +5 uy tín cho người báo cáo vì đã báo cáo chính xác
        if ($report->user) {
            $report->user->increment('reputation_score', 5);
        }

        // Nếu report là về đánh giá (Review) → ẩn review và resolve tất cả reports còn lại
        if ($report->reportable_type === 'App\\Models\\Review' && $report->reportable) {
            $review = $report->reportable;
            if ($review->status !== 'hidden') {
                $review->update(['status' => 'hidden']);
            }
            // Resolve các reports pending khác của review này
            $review->reports()->where('status', 'pending')->update(['status' => 'resolved']);

            return back()->with('success', 'Báo cáo đã xử lý. Đánh giá vi phạm đã được ẩn, người báo cáo được +5 uy tín.');
        }

        return back()->with('success', 'Báo cáo đã được xác nhận, người báo cáo được +5 uy tín.');
    }

    /**
     * Bác bỏ báo cáo (dismissed — không có vấn đề).
     * Trừ -15 uy tín của người báo cáo.
     */
    public function dismiss(Report $report)
    {
        $report->update(['status' => 'dismissed']);

        // -15 uy tín vì báo cáo không phù hợp
        if ($report->user) {
            $report->user->decrement('reputation_score', 15);
        }

        return back()->with('success', 'Báo cáo đã bị bác bỏ, uy tín người báo cáo bị trừ 15 điểm.');
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

    /**
     * Phạt thủ công: cấm người dùng sử dụng tính năng báo cáo.
     * Admin có thể chọn 3 ngày hoặc 7 ngày.
     */
    public function banReporter(Report $report, Request $request)
    {
        $days = in_array((int) $request->input('days'), [3, 7]) ? (int) $request->input('days') : 3;

        $reporter = $report->user;
        if (!$reporter) {
            return back()->with('error', 'Không tìm thấy người dùng.');
        }

        $reporter->update([
            'report_banned_until' => now()->addDays($days),
        ]);

        return back()->with('success', "Đã cấm {$reporter->name} sử dụng tính năng báo cáo trong {$days} ngày.");
    }
}
