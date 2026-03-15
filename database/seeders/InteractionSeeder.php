<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InteractionSeeder extends Seeder
{
    /**
     * Seed watchlist, likes, comments, follows.
     */
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        $movies = Movie::pluck('id')->toArray();
        $reviews = Review::where('status', 'published')->get();

        $this->seedWatchlists($users, $movies);
        $this->seedLikes($users, $reviews);
        $this->seedComments($users, $reviews);
        $this->seedFollows($users);
    }

    /**
     * Mỗi user lưu 5-15 phim vào watchlist.
     */
    protected function seedWatchlists(array $users, array $movies): void
    {
        $statuses = ['want_to_watch', 'watching', 'watched', 'dropped'];
        $count = 0;

        foreach ($users as $userId) {
            $movieCount = rand(5, 15);
            $selectedMovies = (array) array_rand(array_flip($movies), min($movieCount, count($movies)));

            foreach ($selectedMovies as $movieId) {
                // 40% want_to_watch, 10% watching, 40% watched, 10% dropped
                $roll = rand(1, 10);
                $status = match (true) {
                    $roll <= 4 => 'want_to_watch',
                    $roll <= 5 => 'watching',
                    $roll <= 9 => 'watched',
                    default => 'dropped',
                };

                Watchlist::updateOrCreate([
                    'user_id' => $userId,
                    'movie_id' => $movieId,
                ], [
                    'status' => $status,
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Tạo {$count} watchlist entries!");
    }

    /**
     * Random likes cho reviews. Mỗi review được 0-10 likes.
     */
    protected function seedLikes(array $users, $reviews): void
    {
        $count = 0;

        foreach ($reviews as $review) {
            $likeCount = rand(0, min(10, count($users)));
            if ($likeCount === 0)
                continue;

            $likers = (array) array_rand(array_flip($users), $likeCount);

            foreach ($likers as $userId) {
                // Không tự like review của mình
                if ($userId == $review->user_id)
                    continue;

                Like::firstOrCreate([
                    'user_id' => $userId,
                    'review_id' => $review->id,
                ]);
                $count++;
            }

            // Cập nhật likes_count
            $review->update(['likes_count' => $review->likes()->count()]);
        }

        $this->command->info("✅ Tạo {$count} likes!");
    }

    /**
     * Mỗi review phổ biến (có likes) nhận 1-5 comments.
     */
    protected function seedComments(array $users, $reviews): void
    {
        $commentTemplates = [
            'Đồng ý! Review rất hay, mình cũng cảm nhận giống bạn.',
            'Mình thì thấy khác, phim này có nhiều điểm hay hơn bạn nghĩ.',
            'Cảm ơn review, mình quyết định đi xem phim này luôn! 🍿',
            'Review chất lượng, viết rất chi tiết. Follow luôn!',
            'Phim này mình cũng xem rồi, đúng là hay thật. 10/10!',
            'Hmm, mình thấy rating hơi cao. Phim ổn thôi, không đến mức xuất sắc.',
            'Spoiler quá trời! 😭 Nhưng mà review hay, chấp nhận.',
            'Bạn review hay quá! Cho mình hỏi có nên xem ở rạp không?',
            'Phim này mình xem 3 lần rồi mà vẫn thấy hay 😆',
            'Review công tâm, cảm ơn bạn!',
            'Mình cũng đang phân vân, đọc review này quyết định xem luôn.',
            'Diễn viên chính đóng hay thật, xứng đáng được giải!',
            'Soundtrack phim hay không? Mình thích nghe nhạc phim lắm.',
            'Đạo diễn này mình rất thích, phim nào cũng hay!',
            'Review rất khách quan, cảm ơn bạn đã chia sẻ 👍',
        ];

        $count = 0;

        // Chỉ comment vào reviews có likes (phổ biến)
        $popularReviews = $reviews->filter(fn($r) => $r->likes_count > 0);

        foreach ($popularReviews as $review) {
            $commentCount = rand(1, 5);

            for ($i = 0; $i < $commentCount; $i++) {
                $userId = $users[array_rand($users)];

                $comment = Comment::create([
                    'user_id' => $userId,
                    'review_id' => $review->id,
                    'content' => $commentTemplates[array_rand($commentTemplates)],
                ]);
                $count++;

                // 30% chance có reply
                if (rand(1, 10) <= 3) {
                    $replyUserId = $users[array_rand($users)];
                    $replyTemplates = [
                        'Cảm ơn bạn! 😊',
                        'Đúng rồi, mình cũng nghĩ vậy!',
                        'Mình thấy khác hẳn luôn á 😂',
                        'Nên xem ở rạp, trải nghiệm tuyệt vời!',
                        'Mình recommend xem thêm phim này của cùng đạo diễn nhé!',
                    ];

                    Comment::create([
                        'user_id' => $replyUserId,
                        'review_id' => $review->id,
                        'parent_id' => $comment->id,
                        'content' => $replyTemplates[array_rand($replyTemplates)],
                    ]);
                    $count++;
                }
            }

            // Cập nhật comments_count
            $review->update(['comments_count' => $review->comments()->count()]);
        }

        $this->command->info("✅ Tạo {$count} comments (bao gồm replies)!");
    }

    /**
     * Mỗi user follow 3-10 user khác.
     */
    protected function seedFollows(array $users): void
    {
        $count = 0;

        foreach ($users as $userId) {
            $followCount = rand(3, min(10, count($users) - 1));
            $others = array_diff($users, [$userId]);
            $toFollow = (array) array_rand(array_flip($others), $followCount);

            foreach ($toFollow as $followId) {
                DB::table('follows')->insertOrIgnore([
                    'follower_id' => $userId,
                    'following_id' => $followId,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Tạo {$count} follows!");
    }
}
