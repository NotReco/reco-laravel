<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Thảo luận chung',
                'slug' => 'thao-luan-chung',
                'description' => 'Nơi trò chuyện về mọi chủ đề liên quan đến điện ảnh.',
                'order' => 1,
            ],
            [
                'name' => 'Review Phim',
                'slug' => 'review-phim',
                'description' => 'Chia sẻ bài review chi tiết và cảm nhận cá nhân về các bộ phim.',
                'order' => 2,
            ],
            [
                'name' => 'Đề xuất phim',
                'slug' => 'de-xuat-phim',
                'description' => 'Gợi ý phim hay cho cộng đồng hoặc nhờ gợi ý phim.',
                'order' => 3,
            ],
            [
                'name' => 'Hỏi đáp',
                'slug' => 'hoi-dap',
                'description' => 'Hỏi và trả lời câu hỏi về phim, diễn viên, đạo diễn...',
                'order' => 4,
            ],
            [
                'name' => 'Off-topic',
                'slug' => 'off-topic',
                'description' => 'Tán gẫu ngoài lề, giải trí nhẹ nhàng.',
                'order' => 5,
            ],
        ];

        foreach ($categories as $cat) {
            ForumCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
