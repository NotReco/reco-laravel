<?php

namespace App\Services;

use App\Models\BannedWord;
use Illuminate\Support\Facades\Cache;

class ModerationService
{
    /**
     * Kiểm tra nội dung text dựa trên danh sách từ cấm.
     * Rule-based moderation: tìm kiếm các từ có trong bảng banned_words.
     * Cấu trúc chuẩn bị sẵn cho việc gọi AI API sau này (5B).
     *
     * @param string $content
     * @return array
     */
    public function check(string $content): array
    {
        $bannedWords = $this->getBannedWords();
        
        $matchedWords = [];
        $highestSeverity = null;
        $suggestedAction = null;

        // Định nghĩa trọng số để so sánh severity/action
        $severityWeight = ['low' => 1, 'medium' => 2, 'high' => 3];
        $actionWeight = ['pending' => 1, 'hide' => 2, 'delete' => 3];

        $contentLower = mb_strtolower($content, 'UTF-8');

        foreach ($bannedWords as $wordModel) {
            $word = mb_strtolower($wordModel->word, 'UTF-8');
            
            // Tìm từ cấm trong nội dung (có thể cần regex để tìm chính xác từ nguyên vẹn thay vì chuỗi con, 
            // nhưng tạm dùng mb_strpos cho đơn giản và theo yêu cầu).
            if (mb_strpos($contentLower, $word, 0, 'UTF-8') !== false) {
                $matchedWords[] = $wordModel->word;

                // So sánh và lưu lại severity/action cao nhất
                if (!$highestSeverity || $severityWeight[$wordModel->severity] > $severityWeight[$highestSeverity]) {
                    $highestSeverity = $wordModel->severity;
                }

                if (!$suggestedAction || $actionWeight[$wordModel->action] > $actionWeight[$suggestedAction]) {
                    $suggestedAction = $wordModel->action;
                }
            }
        }

        if (!empty($matchedWords)) {
            return [
                'is_clean' => false,
                'matched_words' => array_unique($matchedWords),
                'severity' => $highestSeverity,
                'action' => $suggestedAction,
                'message' => $this->generateMessage($suggestedAction),
                'source' => 'rule',
                'categories' => [],
                'confidence' => 1.0,
            ];
        }

        // Nếu rule-based sạch, kiểm tra xem AI có được bật không
        if (config('moderation.ai_enabled')) {
            $aiService = app(\App\Services\AiModerationService::class);
            $aiResult = $aiService->check($content);
            
            // Nếu AI trả về kết quả fallback (sạch do lỗi) hoặc sạch thật
            if ($aiResult['is_clean']) {
                return $this->cleanResult();
            }

            // Nếu AI bắt được vi phạm
            return $aiResult;
        }

        return $this->cleanResult();
    }

    /**
     * Trả về kết quả sạch (Mặc định).
     */
    protected function cleanResult(): array
    {
        return [
            'is_clean' => true,
            'matched_words' => [],
            'severity' => null,
            'action' => 'allow',
            'message' => 'Nội dung hợp lệ.',
            'source' => 'rule',
            'categories' => [],
            'confidence' => 1.0,
        ];
    }

    /**
     * Lấy danh sách từ cấm đang hoạt động từ Cache (nếu có) hoặc Database.
     */
    protected function getBannedWords()
    {
        return Cache::remember('moderation:banned_words', 3600 * 24, function () {
            return BannedWord::where('is_active', true)->get();
        });
    }

    /**
     * Xóa cache danh sách từ cấm.
     * Gọi hàm này khi admin thêm/sửa/xóa bảng banned_words.
     */
    public function clearCache(): void
    {
        Cache::forget('moderation:banned_words');
    }

    /**
     * Tạo thông báo dựa trên hành động được đề xuất.
     */
    protected function generateMessage(string $action): string
    {
        return match ($action) {
            'delete' => 'Nội dung của bạn chứa từ khóa vi phạm nghiêm trọng và không thể đăng tải.',
            'hide' => 'Nội dung của bạn chứa từ khóa nhạy cảm và sẽ bị ẩn để chờ quản trị viên xem xét.',
            'pending' => 'Nội dung của bạn chứa từ khóa cần xem xét và đang ở trạng thái chờ duyệt.',
            default => 'Nội dung của bạn không hợp lệ.',
        };
    }
}
