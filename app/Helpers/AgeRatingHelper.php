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
            return self::mappedResult('18+', 'Phim dành cho người từ 18 tuổi trở lên', 'bg-red-700 text-white shadow-md border border-red-500/30');
        }

        // 2. Rỗng / không có cert — ẩn badge hoàn toàn
        if (empty($cert)) {
            return self::mappedResult('', '', '');
        }

        $upper = strtoupper(trim($cert));

        // 3. Danh sách giá trị "chưa phân loại" — ẩn badge hoàn toàn
        $unknownValues = [
            'NR', 'N/A', 'NA', 'NOT RATED', 'UNRATED', 'UNKNOWN',
            'CHUA PHAN LOAI', 'CHƯA PHÂN LOẠI', 'UNDEFINED', '-', 'NONE', '',
        ];
        if (in_array($upper, $unknownValues)) {
            return self::mappedResult('', '', '');
        }

        // 4. Phân loại 18+
        if (in_array($upper, ['R', 'NC-17', 'TV-MA', '18+', 'A', 'T18'])) {
            return self::mappedResult('18+', 'Phim dành cho người từ 18 tuổi trở lên', 'bg-red-700 text-white shadow-md border border-red-500/30');
        }

        // 5. Phân loại T16 (15-16 tuổi)
        if (in_array($upper, ['TV-15', '15', 'U/A 16+', '16+', 'T16'])) {
            return self::mappedResult('T16', 'Phù hợp với khán giả từ 16 tuổi trở lên', 'bg-orange-600 text-white shadow-md border border-orange-500/30');
        }

        // 6. Phân loại T13 (13-14 tuổi)
        if (in_array($upper, ['PG-13', 'TV-14', 'U/A 13+', 'T13'])) {
            return self::mappedResult('T13', 'Phù hợp với khán giả từ 13 tuổi trở lên', 'bg-amber-600 text-white shadow-md border border-amber-500/30');
        }

        // 7. Phân loại K (trẻ em)
        if (in_array($upper, ['K'])) {
            return self::mappedResult('K', 'Dành cho trẻ em', 'bg-blue-600 text-white shadow-md border border-blue-400/30');
        }

        // 8. Phân loại P (Mọi độ tuổi)
        if (in_array($upper, ['G', 'PG', 'TV-G', 'TV-Y', 'TV-PG', 'U', 'P'])) {
            return self::mappedResult('P', 'Phù hợp với mọi độ tuổi', 'bg-green-700 text-white shadow-md border border-green-500/30');
        }

        // 9. Fallback — chỉ hiển thị nếu là mã ngắn hợp lệ (tối đa 6 ký tự, toàn ASCII)
        //    Nếu dài hơn hoặc chứa ký tự không phải mã phân loại → ẩn badge
        if (strlen($upper) <= 6 && preg_match('/^[A-Z0-9+\-\/]+$/', $upper)) {
            return self::mappedResult($upper, 'Phân loại: ' . $upper, 'bg-black/70 text-white shadow-md backdrop-blur-sm border border-white/20');
        }

        // 10. Giá trị lạ/dài — ẩn badge
        return self::mappedResult('', '', '');
    }

    private static function mappedResult(string $badge, string $desc, string $color): array
    {
        return [
            'badge'      => $badge,
            'description' => $desc,
            'colorClass'  => $color,
        ];
    }
}
