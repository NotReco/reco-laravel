<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiModerationService
{
    /**
     * Kiểm duyệt nội dung bằng AI (Gemini).
     *
     * @param string $content Nội dung cần kiểm duyệt
     * @param string $type Loại nội dung (vd: 'review', 'comment')
     * @return array Kết quả trả về chứa is_clean, severity, action...
     */
    public function check(string $content, string $type = 'content'): array
    {
        $apiKey = config('moderation.gemini_api_key');
        $model = config('moderation.gemini_model', 'gemini-2.5-flash');
        $timeout = (int) config('moderation.ai_timeout', 8);

        // Nếu thiếu API key, lập tức fallback
        if (empty($apiKey)) {
            return $this->fallbackResult();
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $prompt = $this->buildPrompt($content, $type);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
            ]
        ];

        try {
            $response = Http::timeout($timeout)->post($url, $payload);

            if ($response->failed()) {
                Log::warning('AI Moderation API failed', ['status' => $response->status(), 'body' => $response->body()]);
                return $this->fallbackResult();
            }

            $jsonResponse = $response->json();
            $aiText = $jsonResponse['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($aiText)) {
                return $this->fallbackResult();
            }

            $parsed = json_decode($aiText, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                return $this->fallbackResult();
            }

            return $this->validateAndMapResult($parsed);

        } catch (\Throwable $e) {
            Log::warning('AI Moderation Exception: ' . $e->getMessage());
            return $this->fallbackResult();
        }
    }

    /**
     * Validate JSON trả về từ AI và map lại format chuẩn.
     */
    protected function validateAndMapResult(array $parsed): array
    {
        $isClean = filter_var($parsed['is_clean'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $confidence = (float) ($parsed['confidence'] ?? 1.0);
        $action = strtolower($parsed['action'] ?? 'allow');
        $severity = strtolower($parsed['severity'] ?? 'null');

        // Ngưỡng an toàn: Nếu AI không tự tin (confidence < 0.7), ta không được phạt User (chống false positive)
        if (!$isClean && $confidence < 0.7) {
            return $this->fallbackResult(); // Coi như sạch vì AI phân vân
        }

        // Validate action
        if (!in_array($action, ['allow', 'pending', 'hide', 'delete'])) {
            $action = 'allow';
        }

        if ($isClean) {
            $action = 'allow';
            $severity = null;
        }

        return [
            'is_clean' => $isClean,
            'matched_words' => [], // AI thường phân tích ngữ cảnh, không trả về mảng từ cụ thể
            'severity' => $severity !== 'null' ? $severity : null,
            'action' => $action,
            'message' => $parsed['reason'] ?? 'Nội dung không hợp lệ theo đánh giá của hệ thống AI.',
            'source' => 'ai',
            'categories' => $parsed['categories'] ?? [],
            'confidence' => $confidence,
        ];
    }

    /**
     * Trả về kết quả sạch (Fallback) khi AI lỗi để không chặn nhầm người dùng.
     */
    protected function fallbackResult(): array
    {
        return [
            'is_clean' => true,
            'matched_words' => [],
            'severity' => null,
            'action' => 'allow',
            'message' => '',
            'source' => 'fallback',
            'categories' => [],
            'confidence' => null,
        ];
    }

    /**
     * Tạo prompt gửi tới AI.
     */
    protected function buildPrompt(string $content, string $type): string
    {
        return <<<PROMPT
Bạn là hệ thống kiểm duyệt nội dung (Content Moderator) chuyên nghiệp bằng Tiếng Việt và Tiếng Anh.
Nhiệm vụ của bạn là kiểm tra nội dung ($type) sau đây để phát hiện vi phạm.

CHÚ Ý RẤT QUAN TRỌNG:
Đây có thể là đánh giá phim (movie review) hoặc bình luận cộng đồng. Việc người dùng CHÊ PHIM dở, chê diễn xuất tệ, chê kịch bản nhàm chán, hoặc bày tỏ sự thất vọng về nội dung phim là HOÀN TOÀN HỢP LỆ. KHÔNG ĐƯỢC đánh dấu là vi phạm (toxic/hate) chỉ vì họ đánh giá tiêu cực về phim.

CHỈ ĐÁNH DẤU VI PHẠM nếu nội dung rơi vào một trong các danh mục sau:
- spam (nội dung rác, lặp đi lặp lại vô nghĩa)
- scam (lừa đảo, link mờ ám, lôi kéo tiền bạc)
- ads (quảng cáo sản phẩm/dịch vụ không liên quan)
- toxic (chửi bới cá nhân, xúc phạm người khác trong cộng đồng)
- hate (ngôn từ kích động thù địch, phân biệt chủng tộc/tôn giáo)
- sexual (nội dung khiêu dâm, quấy rối)
- violence (bạo lực, đe dọa)

Trả về kết quả bằng JSON thuần (không có block code ```json) với cấu trúc:
{
  "is_clean": boolean, // true nếu KHÔNG vi phạm, false nếu có vi phạm
  "severity": "low|medium|high|null", // null nếu is_clean = true
  "action": "allow|pending|hide|delete", // allow nếu is_clean = true
  "categories": ["spam", "toxic"], // mảng chứa các danh mục vi phạm, mảng rỗng nếu is_clean = true
  "confidence": float, // Từ 0.0 đến 1.0 (ví dụ 0.95 là rất tự tin)
  "reason": "Giải thích ngắn gọn lý do vi phạm hoặc lý do hợp lệ bằng tiếng Việt"
}

Nội dung cần kiểm tra:
"$content"
PROMPT;
    }
}
