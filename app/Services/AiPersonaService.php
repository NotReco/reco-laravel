<?php

namespace App\Services;

/**
 * AiPersonaService
 *
 * Quản lý giọng nói (tone) và câu trả lời rule-based của Trợ lý RecoDB.
 * Persona: "Người bạn đồng hành yêu phim, nói chuyện ấm áp, tinh tế và có màu sắc điện ảnh."
 *
 * Nguyên tắc:
 *  - Thân thiện, gần gũi, không sáo rỗng, không quá dài.
 *  - Không giảng đạo, không lạm dụng emoji.
 *  - Mỗi câu trả lời vừa đủ cho chatbox mobile.
 *  - Không nhắc tên phim cụ thể nếu không có card đi kèm.
 *  - Không tự suy đoán bệnh/tâm lý nặng của user.
 */
class AiPersonaService
{
    // ──────────────────────────────────────────────────────────────────────
    // Greeting
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Trả về một câu chào ngẫu nhiên, ấm áp. Không card phim.
     */
    public function greeting(): string
    {
        $variants = [
            "Chào bạn 👋 Mình là Trợ lý RecoDB. Mình có thể giúp bạn tìm phim, chọn phim theo tâm trạng, xem review cộng đồng hoặc gợi ý phim lẻ/phim bộ phù hợp. Hôm nay bạn muốn xem gì?",
            "Chào bạn 👋 Rất vui được gặp bạn ở RecoDB. Hôm nay mình có thể giúp bạn tìm một bộ phim hợp tâm trạng, xem review hoặc khám phá phim đang nổi bật.",
            "Xin chào, mình là Trợ lý RecoDB 🎬 Bạn muốn tìm phim lẻ, phim bộ, review hay một gợi ý theo mood hôm nay?",
            "Hey, chào bạn! 👋 Mình là Trợ lý RecoDB — luôn sẵn sàng giúp bạn tìm phim, đọc review hoặc gợi ý phim theo tâm trạng. Bạn cần gì nào?",
        ];

        return $variants[array_rand($variants)];
    }

    // ──────────────────────────────────────────────────────────────────────
    // Acknowledgement (cảm ơn / ok / được rồi)
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Trả lời khi user cảm ơn / xác nhận. Không card phim.
     */
    public function ack(): string
    {
        $variants = [
            "Không có gì nhé 😄 Khi nào bạn cần tìm một bộ phim hợp mood, xem review hoặc khám phá phim mới, mình luôn sẵn sàng.",
            "Rất vui vì đã giúp được bạn! Khi nào muốn tìm phim hay xem review, cứ gọi mình nhé 🎬",
            "Hehe, không có chi 😄 Mình ở đây bất cứ khi nào bạn muốn khám phá phim hoặc cần một gợi ý hợp mood.",
        ];

        return $variants[array_rand($variants)];
    }

    // ──────────────────────────────────────────────────────────────────────
    // Smalltalk (sub-categories: identity, personal, emotional, joke)
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Xử lý các câu hỏi smalltalk với EQ.
     * Tự phân loại sub-category dựa trên nội dung message.
     * Không nhắc tên phim cụ thể, không card.
     *
     * @param string $message Raw user message (chưa normalize).
     */
    public function smalltalk(string $message): string
    {
        $normalized = $this->normalize($message);

        // ── Sub-category: identity ──────────────────────────────────────
        if ($this->containsAny($normalized, ['ban la ai', 'ban la gi', 'may la ai', 'ai vay', 'la ai vay'])) {
            return $this->pickRandom([
                "Mình là Trợ lý RecoDB — người bạn đồng hành giúp bạn tìm phim, xem review cộng đồng, chọn phim theo thể loại hoặc theo tâm trạng. Bạn có thể hỏi mình kiểu: 'Gợi ý phim hài', 'Có phim bộ nào hay không?' hoặc 'Phim nào hợp gu tôi?'",
                "Mình là Trợ lý RecoDB 🎬 Mình giúp bạn khám phá phim lẻ, phim bộ, đọc review và chọn phim theo mood. Cứ hỏi mình bất cứ điều gì về phim nhé!",
            ]);
        }

        // ── Sub-category: capabilities ──────────────────────────────────
        if ($this->containsAny($normalized, ['ban lam duoc gi', 'lam gi duoc', 'giup gi duoc', 'co the lam gi'])) {
            return "Mình có thể giúp bạn tìm phim theo thể loại, gợi ý phim theo tâm trạng, xem review từ cộng đồng, tìm phim của diễn viên/đạo diễn yêu thích hoặc khám phá phim đang hot trên RecoDB. Bạn cứ hỏi thoải mái nhé!";
        }

        // ── Sub-category: emotional ─────────────────────────────────────
        // QUAN TRỌNG: Không nhắc tên phim cụ thể, chỉ gợi ý chung chung.
        // Không tự suy đoán bệnh/tâm lý nặng.
        if ($this->containsAny($normalized, ['buon qua', 'toi buon', 'buon wa', 'buon lam', 'chan qua', 'toi chan', 'met qua', 'toi met', 'co don', 'tu ti'])) {
            return $this->pickRandom([
                "Nghe vậy mình cũng thấy hơi chùng xuống cùng bạn. Nếu hôm nay bạn muốn một bộ phim để nhẹ lòng hơn, mình có thể gợi ý vài phim ấm áp, hài nhẹ hoặc chữa lành trong RecoDB.",
                "Hôm nay có vẻ hơi mệt nhỉ. Nếu bạn muốn, mình có thể gợi ý vài bộ phim nhẹ nhàng để thư giãn. Cứ nói mình biết nhé.",
                "Nghe có vẻ hôm nay bạn hơi chùng xuống. Đôi khi một bộ phim hay cũng giúp mình cảm thấy tốt hơn — bạn muốn mình gợi ý không?",
            ]);
        }

        // ── Sub-category: personal (vui nhẹ) ────────────────────────────
        if ($this->containsAny($normalized, ['dep trai', 'xinh gai', 'xinh khong', 'dep khong'])) {
            return $this->pickRandom([
                "Haha, câu này mình không dám chấm điểm đâu 😄 Nhưng nếu bạn muốn một bộ phim hợp vibe tự tin, cuốn hút hoặc hài hước thì mình có thể gợi ý ngay.",
                "Mình không có mắt để ngắm nhưng chắc chắn là bạn tỏa sáng rồi đó 😄 Muốn mình gợi ý phim nào hợp vibe không?",
            ]);
        }

        if ($this->containsAny($normalized, ['yeu toi', 'yeu khong', 'co yeu'])) {
            return $this->pickRandom([
                "Mình thích... gợi ý phim cho bạn hơn 😄 Nếu bạn đang muốn xem phim tình cảm lãng mạn thì nói mình nhé!",
                "Tình yêu của mình dành cho phim thì nhiều lắm 🎬 Bạn muốn mình gợi ý phim lãng mạn không?",
            ]);
        }

        // ── Sub-category: joke ──────────────────────────────────────────
        if ($this->containsAny($normalized, ['chuyen cuoi', 'ke chuyen', 'hai huoc'])) {
            return "Mình kể chuyện cười hơi dở lắm 😅 Nhưng nếu bạn muốn cười thoải mái thì mình có thể gợi ý vài phim hài cực cuốn trong RecoDB.";
        }

        // ── Default smalltalk ───────────────────────────────────────────
        return $this->pickRandom([
            "Câu này hơi ngoài chuyên môn của mình rồi 😄 Mình là Trợ lý RecoDB nên sẽ hữu ích nhất khi bạn cần tìm phim, xem review hoặc chọn phim theo mood.",
            "Hmm, câu này mình chưa trả lời được 😄 Nhưng nếu bạn muốn tìm phim, xem review hay khám phá phim theo tâm trạng thì mình sẵn sàng giúp ngay!",
            "Mình hơi lệch sóng ở câu này rồi 😄 Mình là Trợ lý RecoDB nên sẽ hữu ích nhất khi bạn cần tìm phim, xem review, chọn phim theo mood hoặc khám phá phim lẻ/phim bộ.",
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Site Help
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Trả lời khi user hỏi web có gì. Không card phim.
     */
    public function siteHelp(): string
    {
        $variants = [
            "RecoDB là nơi bạn có thể khám phá phim lẻ, phim bộ và review cộng đồng trong một không gian dành cho người mê phim. Bạn có thể tìm phim, đọc đánh giá, viết review, lưu phim vào yêu thích/watchlist, xem gợi ý cá nhân hóa và hỏi Trợ lý RecoDB để chọn phim theo thể loại, diễn viên hoặc tâm trạng.",
            "RecoDB là nền tảng khám phá và chia sẻ phim dành cho người Việt mê phim ảnh. Bạn có thể tìm kiếm phim, đọc review cộng đồng, lưu phim yêu thích, xây watchlist và nhận gợi ý cá nhân hóa. Cứ hỏi mình nếu bạn cần hướng dẫn thêm nhé!",
        ];

        return $variants[array_rand($variants)];
    }

    // ──────────────────────────────────────────────────────────────────────
    // Review Help
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Hướng dẫn viết review hay. Không card phim.
     */
    public function reviewHelp(): string
    {
        $variants = [
            "Để viết một review phim cuốn hơn trên RecoDB, bạn có thể thử công thức ngắn này:\n\n1. Mở đầu bằng cảm xúc chung sau khi xem.\n2. Nhắc 1-2 điểm nổi bật như nội dung, diễn xuất, hình ảnh hoặc âm nhạc.\n3. Tránh spoil các cú twist quan trọng.\n4. Chấm điểm công bằng theo trải nghiệm thật của bạn.\n\nMột review hay không cần quá dài, chỉ cần thật và có góc nhìn riêng.",
            "Bí quyết viết review trên RecoDB:\n\n1. Chia sẻ cảm xúc thật sau khi xem — vui, buồn, bất ngờ hay thất vọng.\n2. Highlight 1-2 điểm đặc biệt: diễn xuất, kịch bản, hình ảnh, nhạc phim.\n3. Đừng spoil twist — giữ bất ngờ cho người đọc.\n4. Chấm điểm trung thực, đừng ngại cho điểm thấp nếu phim chưa tốt.\n\nReview ngắn gọn, chân thật sẽ luôn được cộng đồng đánh giá cao!",
        ];

        return $variants[array_rand($variants)];
    }

    // ──────────────────────────────────────────────────────────────────────
    // Irrelevant (ngoài phạm vi)
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Trả lời mềm khi câu hỏi ngoài phạm vi hẳn. Không card phim.
     */
    public function irrelevant(): string
    {
        $variants = [
            "Mình hơi lệch sóng ở câu này rồi 😄 Mình là Trợ lý RecoDB nên sẽ hữu ích nhất khi bạn cần tìm phim, xem review, chọn phim theo mood hoặc khám phá phim lẻ/phim bộ.",
            "Câu này ngoài khả năng của mình rồi 😄 Mình là Trợ lý RecoDB, chuyên giúp bạn tìm phim, đọc review cộng đồng hoặc gợi ý phim theo tâm trạng. Bạn cần gì về phim thì cứ hỏi nhé!",
            "Hmm, câu này mình không giúp được rồi 😄 Nhưng nếu bạn muốn tìm phim hay, xem review hoặc chọn phim theo thể loại, mình luôn sẵn sàng!",
        ];

        return $variants[array_rand($variants)];
    }

    // ──────────────────────────────────────────────────────────────────────
    // Mood intro (câu mở đầu khi gợi ý phim theo mood)
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Câu mở đầu khi gợi ý phim theo mood. Dùng làm intro cho formatContextItemsResponse.
     *
     * @param string $mood Mood key (buon, vui, cang_nao, etc.)
     */
    public function moodIntro(string $mood): string
    {
        $intros = [
            'buon' => [
                "Có chứ. Nếu hôm nay bạn muốn một câu chuyện lắng hơn một chút, RecoDB có vài gợi ý hợp mood này:\n",
                "Những lúc muốn lắng lại, một bộ phim giàu cảm xúc có thể là lựa chọn hay. Mình gợi ý vài phim cho bạn:\n",
            ],
            'cam_dong' => [
                "Nếu bạn đang tìm một câu chuyện chạm đến trái tim, mình có vài gợi ý từ RecoDB:\n",
                "Phim cảm động thì RecoDB không thiếu đâu. Bạn thử xem mấy phim này nhé:\n",
            ],
            'khoc' => [
                "Đôi khi khóc cũng là cách để nhẹ lòng. Mình gợi ý vài phim nhiều cảm xúc trong RecoDB:\n",
                "Nếu bạn muốn một bộ phim khiến mình rưng rưng, thử mấy phim này nhé:\n",
            ],
            'nhe_nhang' => [
                "Phim nhẹ nhàng để thư giãn thì mình có vài gợi ý hay cho bạn:\n",
                "Đây rồi! Vài bộ phim nhẹ nhàng, dễ chịu từ RecoDB cho bạn:\n",
            ],
            'chua_lanh' => [
                "Phim chữa lành thì RecoDB có khá nhiều lựa chọn ấm áp. Bạn thử xem mấy phim này:\n",
                "Nếu bạn cần một bộ phim để chữa lành, mình gợi ý vài phim ấm áp từ RecoDB:\n",
            ],
            'am_ap' => [
                "Phim ấm áp luôn là lựa chọn tuyệt vời. Mình gợi ý cho bạn vài phim từ RecoDB:\n",
                "Đây là vài bộ phim ấm áp mình tìm được trong RecoDB, hy vọng bạn thích:\n",
            ],
            'chill' => [
                "Chill thì phải xem phim nhẹ nhàng rồi! Mình gợi ý vài phim cho bạn:\n",
                "Mood chill thì mấy phim này hợp lắm nè:\n",
            ],
            'vui' => [
                "Đây rồi! Nếu bạn đang muốn cười thoải mái, thử mấy phim này nhé:\n",
                "Phim vui thì RecoDB có nhiều lắm! Bạn xem thử mấy phim này:\n",
            ],
            'hai' => [
                "Phim hài để giải tỏa thì đây là vài gợi ý từ RecoDB:\n",
                "Cười xả stress thì phải xem mấy phim này! Mình gợi ý cho bạn:\n",
            ],
            'giai_toa' => [
                "Muốn giải tỏa thì phim hài là lựa chọn số một! Mình gợi ý vài phim cho bạn:\n",
                "Đây là vài bộ phim hài giúp bạn giải tỏa stress từ RecoDB:\n",
            ],
            'kich_tinh' => [
                "Nếu bạn muốn hồi hộp đến phút cuối, thử mấy phim gây cấn này nhé:\n",
                "Phim kịch tính để giữ bạn trên ghế từ đầu đến cuối, đây là gợi ý của mình:\n",
            ],
            'cang_nao' => [
                "Nếu bạn muốn thử thách đầu óc một chút, mấy phim này sẽ hợp lắm:\n",
                "Phim căng não thì RecoDB có vài tựa rất đáng xem. Bạn thử nhé:\n",
            ],
            'phieu_luu' => [
                "Phiêu lưu và mạo hiểm! Mình gợi ý vài phim hào hứng cho bạn:\n",
                "Nếu bạn đang muốn phiêu lưu, thử mấy phim đầy hứng khởi này nhé:\n",
            ],
            'hao_hung' => [
                "Đây rồi! Vài bộ phim đầy năng lượng và hào hứng từ RecoDB:\n",
                "Muốn phim hào hứng, cuốn hút thì xem mấy phim này nha:\n",
            ],
            'tinh_cam' => [
                "Phim tình cảm thì mình có vài gợi ý lãng mạn cho bạn đây:\n",
                "Nếu bạn đang muốn xem phim tình cảm, thử mấy phim lãng mạn này nhé:\n",
            ],
        ];

        if (isset($intros[$mood])) {
            return $this->pickRandom($intros[$mood]);
        }

        // Fallback intro chung
        return $this->pickRandom([
            "Dựa trên mood bạn muốn, mình tìm được vài phim hay trong RecoDB:\n",
            "RecoDB có vài gợi ý hợp mood này cho bạn:\n",
        ]);
    }

    /**
     * Message khi không tìm thấy phim theo mood. KHÔNG gọi Gemini, KHÔNG "AI bận".
     */
    public function moodEmpty(): string
    {
        return "Mình chưa tìm thấy phim thật sự hợp mood này trong RecoDB. Bạn có thể thử mood khác như hài, nhẹ nhàng, kịch tính hoặc phim bộ/phim lẻ nhé.";
    }

    // ──────────────────────────────────────────────────────────────────────
    // Sensitive (chủ đề cực đoan, chính trị nhạy cảm)
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Trả lời khi user hỏi quan điểm về Hitler, phát xít, cực đoan...
     */
    public function sensitive(): string
    {
        return "Mình không ủng hộ Hitler, phát xít hay các tư tưởng cực đoan gây hại. Nếu bạn đang muốn tìm phim lịch sử hoặc phim phản ánh chiến tranh dưới góc nhìn phê phán, mình có thể giúp bạn tìm theo hướng đó.";
    }

    // ──────────────────────────────────────────────────────────────────────
    // Adult Content Safety
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Cảnh báo khi người dùng gửi nội dung khiêu dâm.
     */
    public function adultViolation(): string
    {
        return "Mình không hỗ trợ nội dung khiêu dâm hoặc mô tả tình dục trực tiếp. Nếu bạn muốn tìm phim có chủ đề trưởng thành theo độ tuổi, mình có thể gợi ý theo hướng chính kịch, tình cảm hoặc 18+ trong RecoDB.";
    }

    /**
     * Cảnh báo khi người dùng bị Mute vì gửi quá nhiều nội dung vi phạm.
     */
    public function mutedWarning(): string
    {
        return "Bạn đã gửi nhiều nội dung không phù hợp nên Trợ lý RecoDB tạm nghỉ với bạn trong ít phút. Bạn có thể quay lại sau nhé.";
    }

    /**
     * Lời chào khi tìm phim 18+ hợp lệ.
     */
    public function adultMovieIntro(): string
    {
        return "RecoDB có một vài phim dành cho người trưởng thành bạn có thể tham khảo. Mình sẽ chỉ gợi ý ở mức thông tin phim, không mô tả chi tiết nhạy cảm:\n";
    }

    /**
     * Khi không tìm thấy phim 18+ hợp lệ.
     */
    public function adultMovieEmpty(): string
    {
        return "Mình chưa tìm thấy phim 18+ phù hợp trong RecoDB. Bạn có thể thử tìm theo thể loại chính kịch, tình cảm hoặc tâm lý.";
    }

    // ──────────────────────────────────────────────────────────────────────
    // Internal helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Pick a random element from an array.
     */
    private function pickRandom(array $items): string
    {
        return $items[array_rand($items)];
    }

    /**
     * Check if normalized text contains any of the given patterns.
     */
    private function containsAny(string $text, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normalize text: lowercase + strip Vietnamese diacritics.
     */
    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');

        $diacritics = [
            'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a',
            'â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
            'ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
            'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e',
            'ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
            'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
            'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o',
            'ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
            'ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
            'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
            'ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
            'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
            'đ'=>'d',
        ];

        $text = strtr($text, $diacritics);
        return preg_replace('/\s+/', ' ', $text);
    }
}
