<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\BannedWord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ModerationService
{
    /**
     * Kiểm tra nội dung text dựa trên danh sách từ cấm.
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

        $severityWeight = ['low' => 1, 'medium' => 2, 'high' => 3];
        $actionWeight = ['pending' => 1, 'hide' => 2, 'delete' => 3, 'block' => 4];

        $contentLower = mb_strtolower($content, 'UTF-8');

        foreach ($bannedWords as $wordModel) {
            $word = mb_strtolower($wordModel->word, 'UTF-8');
            
            if (mb_strpos($contentLower, $word, 0, 'UTF-8') !== false) {
                $matchedWords[] = $wordModel->word;

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

        if (config('moderation.ai_enabled', true)) {
            $aiService = app(\App\Services\AiModerationService::class);
            $aiResult = $aiService->check($content);
            
            if ($aiResult['is_clean']) {
                return $this->cleanResult();
            }

            return $aiResult;
        }

        return $this->cleanResult();
    }

    /**
     * Helper chuẩn hóa xử lý Moderation cho các Controller (Forum, News, Report, v.v.).
     * Thực hiện kiểm tra, ghi log và trả về cấu trúc để xử lý tiếp (hoặc ném exception).
     *
     * @param string $content Nội dung cần kiểm duyệt
     * @param string $actionPrefix Tiền tố log (vd: 'moderation.forum_thread')
     * @param mixed $target Model mục tiêu (nếu có để ghi log)
     * @param bool $throwIfFailed Ném ValidationException mềm thay vì return (tùy chọn)
     * @return array
     * 
     * @throws ValidationException
     */
    public function moderateContent(string $content, string $actionPrefix, $target = null, bool $throwIfFailed = false): array
    {
        $result = $this->check($content);

        if (!$result['is_clean']) {
            $sourceSuffix = ($result['source'] ?? 'rule') === 'ai' ? 'ai_flagged' : 'flagged';
            $actionName = "{$actionPrefix}.{$sourceSuffix}";

            try {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $actionName,
                    'target_type' => $target ? get_class($target) : null,
                    'target_id' => $target ? $target->id : null,
                    'description' => sprintf(
                        "Nguồn: %s | Phân loại: [%s] | Mức độ: %s | Hành động: %s | Tự tin: %s | Từ khóa: [%s]\nNội dung: %s",
                        strtoupper($result['source'] ?? 'rule'),
                        implode(', ', $result['categories'] ?? []),
                        $result['severity'] ?? 'N/A',
                        $result['action'] ?? 'N/A',
                        $result['confidence'] ?? 'N/A',
                        implode(', ', $result['matched_words'] ?? []),
                        \Illuminate\Support\Str::limit($content, 120)
                    ),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Moderation Log Failed: " . $e->getMessage());
            }

            if ($throwIfFailed && in_array($result['action'], ['hide', 'delete', 'pending', 'block'])) {
                // Trả validation message mềm, thân thiện
                $msg = $this->getFriendlyMessage($result['action'], $result['source']);
                throw ValidationException::withMessages([
                    'content' => $msg
                ]);
            }
        }

        return $result;
    }

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

    protected function getBannedWords()
    {
        return Cache::remember('moderation:banned_words', 3600 * 24, function () {
            return BannedWord::where('is_active', true)->get();
        });
    }

    public function clearCache(): void
    {
        Cache::forget('moderation:banned_words');
    }

    protected function generateMessage(string $action): string
    {
        return match ($action) {
            'delete', 'block' => 'Nội dung của bạn chứa từ khóa vi phạm nghiêm trọng và không thể đăng tải.',
            'hide' => 'Nội dung của bạn chứa từ khóa nhạy cảm và sẽ bị ẩn để chờ quản trị viên xem xét.',
            'pending' => 'Nội dung của bạn chứa từ khóa cần xem xét và đang ở trạng thái chờ duyệt.',
            default => 'Nội dung của bạn không hợp lệ.',
        };
    }

    protected function getFriendlyMessage(string $action, string $source): string
    {
        if ($source === 'ai') {
            return 'Nội dung của bạn có dấu hiệu không phù hợp với quy tắc cộng đồng. Vui lòng chỉnh sửa lại trước khi gửi.';
        }

        return match ($action) {
            'delete', 'block' => 'Nội dung có dấu hiệu vi phạm quy tắc nghiêm trọng (spam/khiêu dâm). Vui lòng kiểm tra lại.',
            'hide', 'pending' => 'Nội dung của bạn có dấu hiệu chứa từ nhạy cảm. Vui lòng chỉnh sửa lại trước khi gửi.',
            default => 'Nội dung của bạn không hợp lệ.',
        };
    }
}
