<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Lưu review hoặc quick rating cho Movie.
     */
    public function store(Request $request, Movie $movie)
    {
        return $this->handleStore($request, $movie->id, null);
    }

    /**
     * Lưu review hoặc quick rating cho TvShow.
     */
    public function storeTv(Request $request, \App\Models\TvShow $tvShow)
    {
        return $this->handleStore($request, null, $tvShow->id);
    }

    protected function handleStore(Request $request, ?int $movieId, ?int $tvShowId)
    {
        $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:10'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:50'],
        ], [
            'content.required' => 'Nội dung đánh giá là bắt buộc. Vui lòng chia sẻ cảm nhận của bạn về bộ phim.',
            'content.min' => 'Nội dung đánh giá cần tối thiểu 50 ký tự để đảm bảo chất lượng.',
            'rating.required' => 'Bạn chưa chọn điểm đánh giá.',
        ]);

        $query = Review::where('user_id', Auth::id());
        if ($movieId) $query->where('movie_id', $movieId);
        if ($tvShowId) $query->where('tv_show_id', $tvShowId);
        
        $existing = $query->first();

        if ($existing) {
            return back()->withErrors(['rating' => 'Bạn đã đánh giá nội dung này rồi.']);
        }

        // ── Soft warning: kiểm tra lệch điểm/nội dung ──
        $rating = (int) $request->input('rating');
        $content = $request->input('content');
        $warning = $this->detectSentimentMismatch($rating, $content);
        if ($warning) {
            return back()
                ->withInput()
                ->withErrors(['content' => $warning]);
        }

        Review::create([
            'user_id' => Auth::id(),
            'movie_id' => $movieId,
            'tv_show_id' => $tvShowId,
            'title' => $request->input('title'),
            'content' => $content,
            'excerpt' => \Illuminate\Support\Str::limit($content, 100),
            'rating' => $rating,
            'is_spoiler' => $request->boolean('is_spoiler'),
            'status' => 'published',
            'published_at' => now(),
        ]);

        $score = 5; // full review
        Auth::user()->increment('reputation_score', $score);
        
        if ($movieId) {
            $model = Movie::find($movieId);
            $route = route('movies.show', $model);
        } else {
            $model = \App\Models\TvShow::find($tvShowId);
            $route = route('tv-shows.show', $model);
        }

        return redirect($route)->with('success', 'Review đã được đăng thành công!');
    }

    /**
     * Cập nhật review.
     */
    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:10'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:50'],
        ], [
            'content.required' => 'Nội dung đánh giá là bắt buộc.',
            'content.min' => 'Nội dung đánh giá cần tối thiểu 50 ký tự.',
        ]);

        $rating = (int) $request->input('rating');
        $content = $request->input('content');

        // Soft warning cho update cũng áp dụng
        $warning = $this->detectSentimentMismatch($rating, $content);
        if ($warning) {
            return back()->withInput()->withErrors(['content' => $warning]);
        }

        $review->update([
            'title' => $request->input('title'),
            'content' => $content,
            'excerpt' => \Illuminate\Support\Str::limit($content, 100),
            'rating' => $rating,
            'is_spoiler' => $request->boolean('is_spoiler'),
        ]);

        return back()->with('success', 'Đã cập nhật đánh giá thành công! ✨');
    }

    /**
     * Xóa review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công! 🗑️');
    }

    /**
     * Kiểm tra lệch giữa điểm và nội dung (rule-based, không dùng AI).
     * - Điểm cao (>=8) nhưng nội dung chứa nhiều cụm từ tiêu cực → cảnh báo.
     * - Điểm thấp (<=4) nhưng nội dung chứa nhiều cụm từ tích cực → cảnh báo.
     *
     * Dùng cụm từ dài để tránh false positive từ câu phủ định ("không tệ", "không phải kiệt tác").
     * Ngưỡng >=3 để chỉ bắt trường hợp rõ ràng.
     */
    protected function detectSentimentMismatch(int $rating, string $content): ?string
    {
        $contentLower = mb_strtolower($content, 'UTF-8');

        // Dùng cụm từ dài (>=2 chữ) để tránh khớp sai trong câu phủ định
        $negativeWords = [
            'thất vọng',
            'phí thời gian',
            'nhàm chán',
            'nhàm nhẽo',
            'tệ hại',
            'dở tệ',
            'không hay chút',
            'chán ngắt',
            'kịch bản tệ',
            'diễn xuất tệ',
            'diễn xuất dở',
            'không đáng xem',
            'không nên xem',
            'thất bại hoàn toàn',
            'rất chán',
        ];

        $positiveWords = [
            'xuất sắc',
            'tuyệt vời',
            'đáng xem lắm',
            'kiệt tác',
            'hoàn hảo',
            'tuyệt đỉnh',
            'rất hay',
            'cực hay',
            'đỉnh cao',
            'không thể bỏ lỡ',
            'recommend',
            'phải xem',
            'xứng đáng',
            'ấn tượng sâu sắc',
            'cực kỳ hay',
        ];

        $negCount = 0;
        foreach ($negativeWords as $word) {
            if (mb_strpos($contentLower, $word, 0, 'UTF-8') !== false) {
                $negCount++;
            }
        }

        $posCount = 0;
        foreach ($positiveWords as $word) {
            if (mb_strpos($contentLower, $word, 0, 'UTF-8') !== false) {
                $posCount++;
            }
        }

        // Ngưỡng >=4 để chỉ bắt trường hợp thực sự rõ ràng, tránh false positive từ câu phủ định
        if ($rating >= 8 && $negCount >= 4) {
            return "⚠️ Cảnh báo: Điểm bạn chấm ({$rating}/10) rất cao, nhưng nội dung có vẻ chứa nhiều từ tiêu cực. Vui lòng kiểm tra lại điểm hoặc nội dung để đảm bảo review chính xác. Nếu bạn chắc chắn, vui lòng điều chỉnh nội dung rõ ràng hơn.";
        }

        if ($rating <= 4 && $posCount >= 4) {
            return "⚠️ Cảnh báo: Điểm bạn chấm ({$rating}/10) khá thấp, nhưng nội dung có vẻ chứa nhiều từ tích cực. Vui lòng kiểm tra lại điểm hoặc nội dung để đảm bảo review chính xác.";
        }

        return null;
    }
}
