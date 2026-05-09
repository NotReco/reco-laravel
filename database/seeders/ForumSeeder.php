<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
                'name' => 'Tin tức điện ảnh',
                'slug' => 'tin-tuc-dien-anh',
                'description' => 'Cập nhật tin tức nóng hổi về thế giới điện ảnh.',
                'order' => 5,
            ],
            [
                'name' => 'Tìm phim',
                'slug' => 'tim-phim',
                'description' => 'Tìm kiếm bộ phim bạn đã quên tên qua nội dung, hình ảnh.',
                'order' => 6,
            ],
            [
                'name' => 'Góc diễn viên & đạo diễn',
                'slug' => 'goc-dien-vien-dao-dien',
                'description' => 'Bàn luận về kỹ năng diễn xuất, phong cách làm phim.',
                'order' => 7,
            ],
            [
                'name' => 'Off-topic',
                'slug' => 'off-topic',
                'description' => 'Tán gẫu ngoài lề, giải trí nhẹ nhàng.',
                'order' => 8,
            ],
        ];

        foreach ($categories as $cat) {
            ForumCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $allCategories = ForumCategory::all();

        $topics = [
            'thao-luan-chung' => [
                'Phim nào khiến bạn khóc nhiều nhất?',
                'Ai là diễn viên xuất sắc nhất mọi thời đại?',
                'Điểm khác biệt giữa phim bộ và phim lẻ?',
                'Cảm nghĩ về CGI trong các bộ phim hiện đại',
                'Nghệ thuật kể chuyện bằng hình ảnh trong điện ảnh',
                'Thể loại phim nào đang thoái trào?',
                'Phim điện ảnh Việt Nam: Đang phát triển hay giậm chân tại chỗ?',
            ],
            'review-phim' => [
                'Review siêu phẩm Dune: Part Two - Đỉnh cao điện ảnh sci-fi!',
                'Cảm nhận về bộ phim "Ký sinh trùng" (Parasite) sau nhiều năm',
                'Oppenheimer: Quá dài hay quá xuất sắc?',
                'Đánh giá nhanh phim Godzilla x Kong: The New Empire',
                'Review phim hài tình cảm rom-com đáng xem nhất năm nay',
                'Tại sao Interstellar lại được yêu thích đến vậy?',
                'Bàn luận về cái kết của Inception: Quay hay đổ?',
            ],
            'de-xuat-phim' => [
                'Xin tư vấn một số phim khoa học viễn tưởng hay',
                'Ai biết phim nào plot twist não nề không?',
                'Cần tìm phim kinh dị nặng đô để xem đêm nay',
                'Gợi ý phim hoạt hình Ghibli cho người mới bắt đầu',
                'Các bộ phim Hàn Quốc về gia đình cảm động nhất',
                'Top 5 phim trinh thám không thể bỏ qua',
                'Tìm phim giải trí cuối tuần nhẹ nhàng',
            ],
            'hoi-dap' => [
                'Có ai biết bài hát ở cuối phim Titanic tên gì không?',
                'Làm sao để phân biệt phim độc lập và phim bom tấn?',
                'Mình không hiểu nội dung phim Tenet, ai giải thích giúp với!',
                'Định dạng IMAX có thực sự đáng tiền hơn 2D thông thường?',
                'Phần post-credit của Marvel có ý nghĩa gì?',
                'Cho hỏi kỹ xảo trong phim Avatar được làm như thế nào?',
            ],
            'off-topic' => [
                'Mọi người thường xem phim rạp hay ở nhà?',
                'Đồ ăn vặt yêu thích khi xem phim của bạn là gì?',
                'Kỷ niệm đáng nhớ nhất khi đi xem phim rạp?',
                'Bạn có thói quen đọc review trước khi xem phim không?',
                'Thảo luận về sở thích sưu tầm đĩa than nhạc phim',
                'Giao lưu kết bạn những người đam mê điện ảnh',
            ],
            'tin-tuc-dien-anh' => [
                'Oscar năm nay sẽ gọi tên ai?',
                'Cập nhật tiến độ phần tiếp theo của siêu bom tấn',
                'Sự kiện thảm đỏ LHP Cannes: Những bộ cánh ấn tượng',
                'Đạo diễn Christopher Nolan công bố dự án mới đầy hứa hẹn',
                'Doanh thu phòng vé tuần qua: Phim nào đang dẫn đầu?',
            ],
            'tim-phim' => [
                'Tìm phim: Cô gái có năng lực siêu phàm và chiếc hộp ma thuật',
                'Phim về chiến tranh thế giới có cảnh máy bay rơi',
                'Nhớ mang máng phim hài đầu năm 2000 có anh chàng béo',
                'Xin tên phim kinh dị Thái Lan búp bê ma',
            ],
            'goc-dien-vien-dao-dien' => [
                'Leonardo DiCaprio và những vai diễn để đời',
                'Phân tích kỹ năng diễn xuất của diễn viên hạng A',
                'Đạo diễn Quentin Tarantino và phong cách làm phim bạo lực độc đáo',
                'Sự nghiệp thăng trầm của một ngôi sao Hollywood',
            ]
        ];

        $repliesPool = [
            'Bài viết rất hay, cảm ơn bạn đã chia sẻ.',
            'Mình hoàn toàn đồng ý với quan điểm này!',
            'Theo mình thì không hẳn như vậy, mỗi người một cảm nhận.',
            'Phim này mình xem đi xem lại mấy lần vẫn thấy hay.',
            'Có ai thấy giống mình không?',
            'Đoạn cuối làm mình bất ngờ thực sự.',
            'Chưa xem nhưng nghe review hấp dẫn quá, cuối tuần phải cày luôn.',
            'Cảm nhận rất sâu sắc, mong bạn viết thêm nhiều review khác.',
            'Mình thì lại thấy phần đầu hay hơn phần sau.',
            'Thực ra đạo diễn cố ý làm vậy để gây tranh cãi đó.',
            'Nhạc phim cũng là một điểm cộng lớn cho tác phẩm này.',
            'Diễn xuất xuất thần quá, xem mà nổi da gà.',
            'Chấm 9/10, trừ 1 điểm vì phim kết thúc quá nhanh =))',
            'Cảm ơn chủ thớt đã gợi ý, phim quá đỉnh.',
            'Bạn nói chuẩn quá, không sai vào đâu được.',
            'Mình xem xong cũng thấy buồn man mác.',
            'Không hiểu sao nhiều người chê, mình thấy rất ổn mà.',
            'Kỹ xảo đoạn đó hơi ảo thật, nhưng tổng thể vẫn chấp nhận được.',
            'Bài phân tích quá chi tiết, đọc cuốn thật sự.',
            'Ủng hộ chủ thớt viết thêm nhiều bài như thế này nữa.',
            'Quan điểm rất thú vị, mở mang tầm mắt.',
            'Một góc nhìn mới mẻ về bộ phim này.',
        ];

        $contentPool = [
            '<p>Chào mọi người, hôm nay mình muốn thảo luận về chủ đề này. Các bạn nghĩ sao?</p><p>Cá nhân mình thấy đây là một khía cạnh rất thú vị và đáng để bàn luận sâu hơn.</p>',
            '<p>Vừa mới xem xong và cảm xúc vẫn còn lâng lâng nên lên đây viết vài dòng chia sẻ với anh em.</p><p>Thực sự là một trải nghiệm điện ảnh tuyệt vời, vượt ngoài mong đợi.</p>',
            '<p>Có ai đồng quan điểm với mình không? Cảm giác như phim bị đánh giá thấp hơn giá trị thực tế của nó.</p>',
            '<p>Bài viết này mang tính chất cá nhân, anh em cứ thoải mái đóng góp ý kiến bên dưới nhé.</p>',
            '<p>Một câu hỏi nhỏ dành cho những ai đã xem tác phẩm này: Bạn ấn tượng với phân cảnh nào nhất?</p>',
            '<p>Tổng hợp một số thông tin thú vị mà có thể bạn chưa biết. Cùng đọc và thảo luận nhé!</p>',
        ];

        foreach ($allCategories as $category) {
            $slug = $category->slug;
            if (isset($topics[$slug])) {
                foreach ($topics[$slug] as $title) {
                    $author = $users->random();
                    
                    // Generate a thread
                    $thread = ForumThread::create([
                        'forum_category_id' => $category->id,
                        'user_id' => $author->id,
                        'title' => $title,
                        'content' => $contentPool[array_rand($contentPool)],
                        'views_count' => rand(10, 500),
                        'is_pinned' => rand(0, 100) < 5 ? true : false,
                        'is_locked' => false,
                        'created_at' => now()->subDays(rand(1, 30))->subHours(rand(1, 24)),
                        'updated_at' => now(),
                    ]);

                    // Generate random replies
                    $numReplies = rand(3, 15);
                    for ($i = 0; $i < $numReplies; $i++) {
                        $replyUser = $users->random();
                        ForumReply::create([
                            'forum_thread_id' => $thread->id,
                            'user_id' => $replyUser->id,
                            'content' => '<p>' . $repliesPool[array_rand($repliesPool)] . '</p>',
                            'created_at' => $thread->created_at->addMinutes(rand(10, 5000)),
                            'updated_at' => now(),
                        ]);
                    }

                    // Touch thread updated_at to simulate latest activity
                    if ($thread->replies()->count() > 0) {
                        $latestReply = $thread->replies()->latest('created_at')->first();
                        $thread->update(['updated_at' => $latestReply->created_at]);
                    }
                }
            }
        }
    }
}
