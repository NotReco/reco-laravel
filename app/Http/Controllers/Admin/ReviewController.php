<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Notifications\ReviewActionNotification;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Danh sách reviews đang bị báo cáo (pending reports).
     */
    public function index(Request $request)
    {
        $query = Review::with([
                'user',
                'movie',
                'tvShow',
                'reports' => fn($q) => $q->where('status', 'pending')->with('user')->latest(),
            ])
            ->withCount(['reports as pending_reports_count' => fn($q) => $q->where('status', 'pending')])
            ->whereHas('reports', fn($q) => $q->where('status', 'pending'));

        // Filter theo loại nội dung (phim hay series)
        if ($request->filled('type')) {
            if ($request->type === 'movie') {
                $query->whereNotNull('movie_id')->whereNull('tv_show_id');
            } elseif ($request->type === 'series') {
                $query->whereNotNull('tv_show_id')->whereNull('movie_id');
            }
        }

        // Tìm kiếm theo user hoặc tên phim
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('movie', fn($m) => $m->where('title', 'like', "%{$q}%"))
                    ->orWhereHas('tvShow', fn($t) => $t->where('title', 'like', "%{$q}%"));
            });
        }

        // Sắp xếp: nhiều report nhất lên đầu
        $query->orderByDesc('pending_reports_count')->orderByDesc('created_at');

        $reviews = $query->paginate(20)->withQueryString();

        // Tổng số đánh giá đang bị report
        $totalFlagged = Review::whereHas('reports', fn($q) => $q->where('status', 'pending'))->count();

        return view('admin.reviews.index', compact('reviews', 'totalFlagged'));
    }

    /**
     * Ẩn review vi phạm, resolve tất cả reports và gửi thông báo cho user.
     */
    public function hide(Review $review)
    {
        $review->load(['movie', 'tvShow', 'user']);

        $review->update(['status' => 'hidden']);
        $review->reports()->where('status', 'pending')->update(['status' => 'resolved']);

        // Gửi thông báo cho chủ review
        if ($review->user) {
            $contentTitle = $review->movie?->title ?? $review->tvShow?->title;
            $review->user->notify(new ReviewActionNotification(
                action: 'hidden',
                reviewTitle: $review->title,
                contentTitle: $contentTitle,
            ));
        }

        return back()->with('success', 'Đánh giá đã được ẩn, báo cáo đã xử lý và người dùng đã được thông báo.');
    }

    /**
     * Bỏ ẩn review (khôi phục hiển thị). Không gửi thông báo — giữ im lặng.
     */
    public function unhide(Review $review)
    {
        $review->update(['status' => 'published']);

        return back()->with('success', 'Đánh giá đã được khôi phục hiển thị.');
    }

    /**
     * Bỏ qua tất cả báo cáo — review vẫn hiển thị bình thường.
     */
    public function dismissReports(Review $review)
    {
        $count = $review->reports()->where('status', 'pending')->count();
        $review->reports()->where('status', 'pending')->update(['status' => 'dismissed']);

        return back()->with('success', "Đã bỏ qua {$count} báo cáo. Đánh giá vẫn hiển thị bình thường.");
    }

    /**
     * Xóa review vĩnh viễn, gửi thông báo cho user trước khi xóa.
     */
    public function destroy(Review $review)
    {
        // Load trước khi xóa để lấy thông tin gửi notification
        $review->load(['movie', 'tvShow', 'user']);

        $user         = $review->user;
        $contentTitle = $review->movie?->title ?? $review->tvShow?->title;
        $reviewTitle  = $review->title;

        // Xóa reports và review
        $review->reports()->delete();
        $review->delete();

        // Gửi thông báo cho chủ review
        if ($user) {
            $user->notify(new ReviewActionNotification(
                action: 'deleted',
                reviewTitle: $reviewTitle,
                contentTitle: $contentTitle,
            ));
        }

        return back()->with('success', 'Đánh giá đã bị xóa vĩnh viễn và người dùng đã được thông báo.');
    }
}
