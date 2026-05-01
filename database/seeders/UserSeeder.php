<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Tạo 30 user giả để có dữ liệu tương tác.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('vi_VN');

        $avatarStyles = ['adventurer', 'avataaars', 'bottts', 'personas', 'pixel-art'];

        for ($i = 1; $i <= 30; $i++) {
            $style = $avatarStyles[array_rand($avatarStyles)];
            $gender = $faker->randomElement(['male', 'female']);
            $name = $faker->name($gender);

            User::updateOrCreate(
                ['email' => "user{$i}@reco.test"],
                [
                    'name' => $name,
                    'password' => 'password',
                    'avatar' => "https://api.dicebear.com/7.x/{$style}/svg?seed=" . urlencode($name),
                    'bio' => $faker->optional(0.7)->randomElement([
                        'Mê phim từ nhỏ, đặc biệt thích phim kinh dị 🎃',
                        'Reviewer nghiệp dư, xem phim mỗi tối 🍿',
                        'Sinh viên ngành điện ảnh, yêu phim indie 🎬',
                        'Thích phim Marvel và DC, team cả hai! 🦸',
                        'Phim hay là phải share, review chất là phải đọc ✍️',
                        'Weekend = Netflix + Pizza 🍕',
                        'Xem phim để sống, sống để xem phim 🎥',
                        'Fan cứng của Christopher Nolan 🧠',
                        'Thích anime và phim Nhật 🇯🇵',
                        'Phim tài liệu là tình yêu đích thực 📹',
                    ]),
                    'pronouns' => $gender === 'male' ? 'Anh ấy' : 'Cô ấy',
                    'location' => $faker->optional(0.5)->randomElement([
                        'TP. Hồ Chí Minh',
                        'Hà Nội',
                        'Đà Nẵng',
                        'Cần Thơ',
                        'Huế',
                        'Nha Trang',
                        'Đà Lạt',
                        'Hải Phòng',
                    ]),
                    'date_of_birth' => $faker->dateTimeBetween('-35 years', '-18 years'),
                    'role' => 'user',
                    'is_active' => true,
                ]
            );
        }

        $this->command->info("✅ Tạo 30 user giả thành công!");
    }
}
