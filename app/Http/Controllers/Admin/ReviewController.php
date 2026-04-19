<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Danh sách reviews — filter by status.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'movie'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            })->orWhereHas('movie', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%');
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Duyệt review.
     */
    public function approve(Review $review)
    {
        $review->update(['status' => 'published']);

        return back()->with('success', 'Review đã được duyệt và công khai.');
    }

    /**
     * Từ chối review.
     */
    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);

        return back()->with('success', 'Review đã bị từ chối.');
    }

    /**
     * Xóa review.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review đã bị xóa.');
    }
}
