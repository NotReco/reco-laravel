<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\TvShow;
use Illuminate\Database\Eloquent\Model;

class AdultContentDetectionService
{
    /**
     * Keywords rất mạnh về nội dung 18+.
     */
    protected array $strongKeywords = [
        'sex', 'sexual', 'sexuality', 'erotic', 'erotica', 'pornography', 'pornographic',
        'nymphomania', 'nymphomaniac', 'prostitution', 'brothel', 'stripper', 'strip club',
        'nudity', 'explicit', 'adult', 'sadomasochism', 'bdsm', 'fetish', 'incest',
        'rape', 'sexual abuse', 'sex addiction', 'affair', 'adultery'
    ];

    /**
     * Keywords nhẹ/vừa về tình cảm, không tự động làm phim thành 18+.
     */
    protected array $lightKeywords = [
        'romance', 'romantic', 'love', 'relationship', 'couple', 'marriage',
        'drama', 'passion', 'desire', 'seduction', 'temptation'
    ];

    /**
     * Title keywords rất mạnh để check title/original_title.
     */
    protected array $titleStrongKeywords = [
        'nymphomaniac', 'sex', 'erotic', 'porn', 'adult'
    ];

    /**
     * Genres cần check nhẹ.
     */
    protected array $lightGenres = [
        'romance', 'drama', 'phim lãng mạn', 'phim chính kịch'
    ];

    public function analyzeMovie(Movie $movie): array
    {
        return $this->analyzeMedia($movie);
    }

    public function analyzeTvShow(TvShow $tvShow): array
    {
        return $this->analyzeMedia($tvShow);
    }

    /**
     * Phân tích các tín hiệu heuristic để dự đoán 18+.
     */
    protected function analyzeMedia(Model $media): array
    {
        $score = 0;
        $signals = [];
        $confidence = 0;

        // 1. Check Age Rating (Đã là 18+ hoặc R, NC-17...)
        if (method_exists($media, 'isAdultRated') && $media->isAdultRated()) {
            $score += 100;
            $signals[] = "Current rating is already adult ({$media->age_rating})";
            $confidence += 50;
        }

        // 2. TMDB Adult flag (nếu hệ thống có trường này, tuy hiện tại DB chưa có nhưng để dự phòng)
        if (isset($media->adult) && $media->adult === true) {
            $score += 80;
            $signals[] = "Has TMDB adult flag";
            $confidence += 40;
        }

        // Prepare text sources
        $title = mb_strtolower($media->title ?? '', 'UTF-8');
        $originalTitle = mb_strtolower($media->original_title ?? '', 'UTF-8');
        $synopsis = mb_strtolower($media->synopsis ?? '', 'UTF-8');

        // Lấy genres và tags nếu model có tải sẵn hoặc lazy load
        // Để tránh N+1, ta nên check if relation loaded hoặc query luôn, nhưng ở command ta sẽ eagerly load
        $genres = $media->genres ? $media->genres->pluck('name')->map(fn($g) => mb_strtolower($g, 'UTF-8'))->toArray() : [];
        $tags = $media->tags ? $media->tags->pluck('name')->map(fn($t) => mb_strtolower($t, 'UTF-8'))->toArray() : [];

        // 3. Keyword mạnh trong Title / Original Title
        foreach ($this->titleStrongKeywords as $kw) {
            $pattern = '/\b' . preg_quote($kw, '/') . '\b/i';
            if (preg_match($pattern, $title) || preg_match($pattern, $originalTitle)) {
                $score += 70;
                $signals[] = "Strong title keyword: {$kw}";
                $confidence += 30;
                // Cộng 1 lần cho title là đủ mạnh
                break;
            }
        }

        // 4. Keyword mạnh trong TMDB keywords (Tags)
        $matchedTagStrong = array_intersect($tags, $this->strongKeywords);
        if (!empty($matchedTagStrong)) {
            $score += 50;
            $signals[] = "Strong TMDB keyword: " . implode(', ', $matchedTagStrong);
            $confidence += 30;
        }

        // 5. Keyword mạnh trong Synopsis
        foreach ($this->strongKeywords as $kw) {
            $pattern = '/\b' . preg_quote($kw, '/') . '\b/i';
            if (preg_match($pattern, $synopsis)) {
                $score += 40;
                $signals[] = "Strong synopsis keyword: {$kw}";
                $confidence += 20;
                // Cộng 1 lần cho synopsis
                break;
            }
        }

        // 6. Từ khóa nhẹ trong Tags/Synopsis/Title
        $matchedLight = array_intersect($tags, $this->lightKeywords);
        $foundLightInText = false;
        foreach ($this->lightKeywords as $kw) {
            $pattern = '/\b' . preg_quote($kw, '/') . '\b/i';
            if (preg_match($pattern, $synopsis) || preg_match($pattern, $title) || preg_match($pattern, $originalTitle)) {
                $matchedLight[] = $kw;
                $foundLightInText = true;
            }
        }

        if (!empty($matchedLight) || $foundLightInText) {
            $score += 15;
            $signals[] = "Light keywords found (romance/drama...)";
            $confidence += 10;
        }

        // 7. Genre Romance / Drama
        $matchedGenres = array_intersect($genres, $this->lightGenres);
        if (!empty($matchedGenres)) {
            $score += 5;
            $signals[] = "Genre contains romance/drama";
            $confidence += 10;
        }

        // Normalize confidence
        $confidence = min(100, $confidence);

        // Determine Risk Level & Recommendation
        $riskLevel = 'low';
        $suggestedAgeRating = null;
        $isAdultLikely = false;

        if ($score >= 70) {
            $riskLevel = 'high';
            $isAdultLikely = true;
            $suggestedAgeRating = '18+';
        } elseif ($score >= 40) {
            $riskLevel = 'medium';
        }

        return [
            'is_adult_likely' => $isAdultLikely,
            'confidence' => $confidence,
            'risk_level' => $riskLevel,
            'matched_signals' => $signals,
            'suggested_age_rating' => $suggestedAgeRating,
            'score' => $score,
            'reason' => "Heuristic score: {$score}. " . implode("; ", $signals)
        ];
    }
}
