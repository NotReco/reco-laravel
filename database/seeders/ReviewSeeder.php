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
        $users = User::query()->where('role', 'user')->pluck('id')->toArray();
        $movies = Movie::query()->whereNotNull('tmdb_id')->get();
        $tvShows = \App\Models\TvShow::query()->whereNotNull('tmdb_id')->get();

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

        $this->command->info("Bắt đầu tạo reviews cho Phim lẻ...");
        $bar = $this->command->getOutput()->createProgressBar($movies->count());
        $bar->start();

        $totalReviews = 0;

        foreach ($movies as $movie) {
            $this->seedReviewsForMedia($movie, 'movie_id', $users, $reviewTemplates, $titleTemplates, $totalReviews);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();

        $this->command->info("Bắt đầu tạo reviews cho Phim bộ...");
        $bar2 = $this->command->getOutput()->createProgressBar($tvShows->count());
        $bar2->start();

        foreach ($tvShows as $tvShow) {
            $this->seedReviewsForMedia($tvShow, 'tv_show_id', $users, $reviewTemplates, $titleTemplates, $totalReviews);
            $bar2->advance();
        }

        $bar2->finish();
        $this->command->newLine(2);
        $this->command->info("✅ Tạo {$totalReviews} reviews!");
    }

    protected function seedReviewsForMedia(\App\Models\Movie|\App\Models\TvShow $media, string $foreignKey, array $users, array $reviewTemplates, array $titleTemplates, int &$totalReviews)
    {
        // Each movie must have at least 1 review
        $reviewCount = rand(4, 8);
        $reviewerIds = (array) array_rand(array_flip($users), min($reviewCount, count($users)));

        foreach ($reviewerIds as $userId) {
            $sentiment = $this->randomSentiment($media);
            $rating = $this->ratingForSentiment($sentiment);
            $title = $titleTemplates[$sentiment][array_rand($titleTemplates[$sentiment])];
            $content = $reviewTemplates[$sentiment][array_rand($reviewTemplates[$sentiment])];

            Review::create([
                'user_id' => $userId,
                $foreignKey => $media->id,
                'title' => $title,
                'excerpt' => Str::limit($content, 100),
                'content' => $content,
                'rating' => $rating,
                'is_spoiler' => rand(1, 10) <= 2,
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 90)),
                'view_count' => rand(10, 500),
            ]);
            $totalReviews++;
        }
    }

    protected function randomSentiment(\App\Models\Movie|\App\Models\TvShow $media): string
    {
        // Danh sách ID các phim/TV Shows nổi tiếng, kinh điển
        $famousIds = [278, 238, 155, 13, 122, 680, 550, 157336, 11, 603, 155, 27205, 597, 109445, 1726, 101, 769, 510, 24428, 1399, 1396, 66732, 93405, 60625, 84958, 60059, 1402, 1416, 85271, 100088, 1424, 76479, 76331, 60574, 94997];
        
        $roll = rand(1, 100);
        
        if (in_array($media->tmdb_id, $famousIds)) {
            // Phim kinh điển: 90% positive, 10% neutral
            if ($roll <= 90) return 'positive';
            return 'neutral';
        }
        
        // Phim thông thường (vốn đã được filter chất lượng cao từ TMDB): 70% positive, 20% neutral, 10% negative
        if ($roll <= 70) return 'positive';
        if ($roll <= 90) return 'neutral';
        return 'negative';
    }

    protected function ratingForSentiment(string $sentiment): int
    {
        return match ($sentiment) {
            'positive' => rand(8, 10),  // 8, 9, 10
            'neutral'  => rand(5, 7),   // 5, 6, 7
            'negative' => rand(2, 4),   // 2, 3, 4
        };
    }
}
