<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use App\Services\TmdbService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    /**
     * Tạo reviews: mix giữa TMDb reviews thật + reviews Faker.
     */
    public function run(): void
    {
        $tmdb = app(TmdbService::class);
        $users = User::where('role', 'user')->pluck('id')->toArray();
        $movies = Movie::whereNotNull('tmdb_id')->get();

        if (empty($users)) {
            $this->command->warn('⚠️  Chưa có user nào. Chạy UserSeeder trước!');
            return;
        }

        $reviewTemplates = [
            'positive' => [
                'Phim xuất sắc! Kịch bản chặt chẽ, diễn xuất tuyệt vời. Đây là một trong những bộ phim đáng xem nhất năm.',
                'Mình xem đi xem lại mà không chán. Nhạc phim hay, hình ảnh đẹp, cốt truyện cuốn hút từ đầu đến cuối.',
                'Quá hay! Từng chi tiết nhỏ đều được chăm chút. Đạo diễn thực sự có tầm nhìn. Recommend 10/10!',
                'Không ngờ phim lại hay đến thế. Ban đầu kỳ vọng không cao nhưng xem xong thì wow, phải nói là ấn tượng.',
                'Phim rất cảm động, mình khóc ở đoạn cuối. Diễn viên chính diễn quá đỉnh, xứng đáng được đề cử Oscar.',
                'Một kiệt tác điện ảnh! Sự kết hợp hoàn hảo giữa hình ảnh, âm thanh và diễn xuất. Phải xem trên màn hình lớn.',
            ],
            'neutral' => [
                'Phim ổn, không quá xuất sắc nhưng cũng đáng xem một lần. Cốt truyện hơi dễ đoán ở nửa sau.',
                'Xem được, giải trí tốt nhưng không đọng lại nhiều. Diễn viên phụ hơi yếu so với vai chính.',
                'Phim có vài điểm hay nhưng tổng thể chưa thuyết phục. Kịch bản cần được trau chuốt hơn.',
                'Trung bình khá, nếu không có gì xem thì đây là lựa chọn ok. Hiệu ứng hình ảnh tốt nhưng cốt truyện mỏng.',
            ],
            'negative' => [
                'Thất vọng so với kỳ vọng. Trailer hấp dẫn nhưng phim chính thì nhàm chán, thiếu điểm nhấn.',
                'Không hiểu sao phim được rating cao thế. Mình thấy kịch bản lủng củng, diễn xuất gượng gạo.',
                'Phim dài quá mà nội dung không đủ để giữ chân. Nửa đầu hay nhưng nửa sau xuống dốc nghiêm trọng.',
            ],
        ];

        $titleTemplates = [
            'positive' => [
                'Tuyệt phẩm không thể bỏ lỡ!',
                'Xứng đáng 5 sao ⭐⭐⭐⭐⭐',
                'Một trong những phim hay nhất mình từng xem',
                'Phim quá đỉnh, phải review ngay!',
                'Masterpiece! Không có lời nào đủ khen',
            ],
            'neutral' => [
                'Xem được nhưng chưa xuất sắc',
                'Ổn, không hơn không kém',
                'Review trung thực sau khi xem',
                'Hơi thất vọng một chút',
            ],
            'negative' => [
                'Không như kỳ vọng...',
                'Tiếc tiền vé rạp',
                'Review thẳng: phim dở',
            ],
        ];

        $bar = $this->command->getOutput()->createProgressBar($movies->count());
        $bar->start();

        $totalReviews = 0;
        $totalQuickRatings = 0;

        foreach ($movies as $movie) {
            // Mỗi phim có 2-6 reviews đầy đủ
            $reviewCount = rand(2, 6);
            $reviewerIds = (array) array_rand(array_flip($users), min($reviewCount, count($users)));

            foreach ($reviewerIds as $userId) {
                // 70% review đầy đủ, 30% quick rating
                if (rand(1, 10) <= 7) {
                    // Full review
                    $sentiment = $this->randomSentiment();
                    $rating = $this->ratingForSentiment($sentiment);
                    $title = $titleTemplates[$sentiment][array_rand($titleTemplates[$sentiment])];
                    $content = $reviewTemplates[$sentiment][array_rand($reviewTemplates[$sentiment])];

                    Review::create([
                        'user_id' => $userId,
                        'movie_id' => $movie->id,
                        'title' => $title,
                        'excerpt' => Str::limit($content, 100),
                        'content' => $content,
                        'rating' => $rating,
                        'is_spoiler' => rand(1, 10) <= 2, // 20% spoiler
                        'status' => 'published',
                        'published_at' => now()->subDays(rand(1, 90)),
                        'view_count' => rand(10, 500),
                    ]);
                    $totalReviews++;
                } else {
                    // Quick rating
                    $rating = rand(40, 100) / 10; // 4.0 - 10.0

                    Review::create([
                        'user_id' => $userId,
                        'movie_id' => $movie->id,
                        'title' => null,
                        'content' => null,
                        'rating' => $rating,
                        'status' => 'published',
                        'published_at' => now()->subDays(rand(1, 60)),
                    ]);
                    $totalQuickRatings++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("✅ Tạo {$totalReviews} reviews + {$totalQuickRatings} quick ratings!");
    }

    protected function randomSentiment(): string
    {
        $roll = rand(1, 10);
        if ($roll <= 5)
            return 'positive';   // 50%
        if ($roll <= 8)
            return 'neutral';    // 30%
        return 'negative';                   // 20%
    }

    protected function ratingForSentiment(string $sentiment): float
    {
        return match ($sentiment) {
            'positive' => rand(75, 100) / 10,  // 7.5 - 10.0
            'neutral' => rand(50, 74) / 10,   // 5.0 - 7.4
            'negative' => rand(20, 49) / 10,   // 2.0 - 4.9
        };
    }
}
