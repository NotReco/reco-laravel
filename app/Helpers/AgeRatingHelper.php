<?php

namespace App\Helpers;

class AgeRatingHelper
{
    /**
     * Chuẩn hóa age rating theo chuẩn Việt Nam.
     * Trả về mảng chứa badge, description và colorClass.
     */
    public static function normalize(?string $cert, bool $adult = false): array
    {
        // 1. Adult ưu tiên tuyệt đối
        if ($adult) {
            return self::mappedResult('18+', 'Phim dành cho người từ 18 tuổi trở lên', 'bg-red-600 text-white shadow-md shadow-red-500/20');
        }

        // 2. Rỗng hoặc NR (Not Rated)
        if (empty($cert) || in_array(strtoupper(trim($cert)), ['NR', 'N/A'])) {
            return self::mappedResult('Chưa phân loại', 'Chưa có dữ liệu phân loại độ tuổi', 'bg-gray-500 text-white shadow-md');
        }

        $upper = strtoupper(trim($cert));

        // 3. Phân loại 18+
        if (in_array($upper, ['R', 'NC-17', 'TV-MA', '18+', 'A'])) {
            return self::mappedResult('18+', 'Phim dành cho người từ 18 tuổi trở lên', 'bg-red-600 text-white shadow-md shadow-red-500/20');
        }

        // 4. Phân loại T16 (15-16 tuổi)
        if (in_array($upper, ['TV-15', '15', 'U/A 16+', '16+', 'T16'])) {
            return self::mappedResult('T16', 'Phù hợp với khán giả từ 16 tuổi trở lên', 'bg-orange-600 text-white shadow-md');
        }

        // 5. Phân loại T13 (13-14 tuổi)
        if (in_array($upper, ['PG-13', 'TV-14', 'U/A 13+', 'T13'])) {
            return self::mappedResult('T13', 'Phù hợp với khán giả từ 13 tuổi trở lên', 'bg-yellow-600 text-white shadow-md');
        }

        // 6. Phân loại P (Mọi độ tuổi)
        if (in_array($upper, ['G', 'PG', 'TV-G', 'TV-Y', 'TV-PG', 'U', 'P'])) {
            return self::mappedResult('P', 'Phù hợp với mọi độ tuổi', 'bg-green-600 text-white shadow-md');
        }

        // 7. Fallback nếu không thuộc các chuẩn trên
        return self::mappedResult($upper, "Phân loại: $upper", 'bg-gray-900/70 backdrop-blur text-gray-200');
    }

    private static function mappedResult(string $badge, string $desc, string $color): array
    {
        return [
            'badge' => $badge,
            'description' => $desc,
            'colorClass' => $color,
        ];
    }
}
