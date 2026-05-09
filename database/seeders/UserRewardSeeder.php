<?php

namespace Database\Seeders;

use App\Models\AvatarFrame;
use App\Models\Quest;
use App\Models\User;
use App\Models\UserQuestProgress;
use App\Models\UserTitle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách bot/user (không phải admin/mod)
        $users = User::whereNotIn('role', ['admin', 'moderator'])->inRandomOrder()->take(50)->get();

        if ($users->isEmpty()) {
            $this->command->info('Không tìm thấy người dùng nào để gắn phần thưởng.');
            return;
        }

        $quests = Quest::where('is_active', true)->get();
        $titles = UserTitle::where('is_active', true)->get();
        $frames = AvatarFrame::where('is_active', true)->get();

        if ($quests->isEmpty() && $titles->isEmpty() && $frames->isEmpty()) {
            $this->command->info('Không có quest, title hoặc frame nào khả dụng.');
            return;
        }

        $this->command->info('Bắt đầu gán phần thưởng cho ' . $users->count() . ' user...');

        foreach ($users as $user) {
            DB::transaction(function () use ($user, $quests, $titles, $frames) {
                // 1. Hoàn thành 1-3 Quest ngẫu nhiên
                if ($quests->isNotEmpty()) {
                    $randomQuests = $quests->random(rand(1, min(3, $quests->count())));
                    foreach ($randomQuests as $quest) {
                        UserQuestProgress::updateOrCreate(
                            ['user_id' => $user->id, 'quest_id' => $quest->id],
                            [
                                'progress'     => $quest->target_value,
                                'completed_at' => now()->subDays(rand(1, 30)),
                                'rewarded_at'  => now()->subDays(rand(1, 29)),
                            ]
                        );

                        // Phát thưởng theo quest
                        if (in_array($quest->reward_type, ['title', 'both']) && $quest->reward_title_id) {
                            $user->titles()->syncWithoutDetaching([$quest->reward_title_id]);
                        }
                        if (in_array($quest->reward_type, ['frame', 'both']) && $quest->reward_frame_id) {
                            $user->frames()->syncWithoutDetaching([$quest->reward_frame_id]);
                        }
                    }
                }

                // 2. Gán ngẫu nhiên thêm Title (0-2 cái)
                if ($titles->isNotEmpty() && rand(1, 100) <= 70) { // 70% có thêm title
                    $randomTitles = $titles->random(rand(1, min(2, $titles->count())));
                    $user->titles()->syncWithoutDetaching($randomTitles->pluck('id')->toArray());
                }

                // 3. Gán ngẫu nhiên thêm Frame (0-2 cái)
                if ($frames->isNotEmpty() && rand(1, 100) <= 70) { // 70% có thêm frame
                    $randomFrames = $frames->random(rand(1, min(2, $frames->count())));
                    $user->frames()->syncWithoutDetaching($randomFrames->pluck('id')->toArray());
                }

                // 4. Set active title và frame từ những cái đang sở hữu
                $ownedTitles = $user->titles()->get();
                if ($ownedTitles->isNotEmpty() && rand(1, 100) <= 80) { // 80% có đeo title
                    $user->active_title_id = $ownedTitles->random()->id;
                }

                $ownedFrames = $user->frames()->get();
                if ($ownedFrames->isNotEmpty() && rand(1, 100) <= 80) { // 80% có đeo frame
                    $user->active_frame_id = $ownedFrames->random()->id;
                }

                $user->save();
            });
        }

        $this->command->info('Đã hoàn tất gắn phần thưởng và trang bị ngẫu nhiên cho users!');
    }
}
