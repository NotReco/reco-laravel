<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    // -------------------------------------------------------------------------
    // Họ phổ biến tại Việt Nam
    // -------------------------------------------------------------------------
    private static array $ho = [
        'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh',
        'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ',
        'Hồ', 'Ngô', 'Dương', 'Lý',
    ];

    // -------------------------------------------------------------------------
    // Tên đệm – Nam (phổ biến, hiện đại)
    // -------------------------------------------------------------------------
    private static array $demNam = [
        'Minh', 'Hải', 'Quang', 'Đức', 'Tuấn', 'Gia',
        'Nhật', 'Thái', 'Trọng', 'Phúc', 'Khoa', 'Anh',
        'Hùng', 'Tiến', 'Hoàng', 'Công',
    ];

    // -------------------------------------------------------------------------
    // Tên đệm – Nữ (phổ biến, hiện đại)
    // -------------------------------------------------------------------------
    private static array $demNu = [
        'Thanh', 'Thu', 'Ngọc', 'Mai', 'Thị', 'Bảo',
        'Diễm', 'Hồng', 'Trúc', 'Phương', 'Khánh', 'Quỳnh',
        'Tuyết', 'Uyên', 'Nhã', 'Bích',
    ];

    // -------------------------------------------------------------------------
    // Tên chính – Nam (hiện đại, tự nhiên)
    // -------------------------------------------------------------------------
    private static array $tenNam = [
        'Đăng', 'Trí', 'Bách', 'Huy', 'Anh', 'Kiệt',
        'Bảo', 'Nghĩa', 'Khôi', 'Phong', 'Sơn', 'Lâm',
        'Hải', 'Dũng', 'Quân', 'Khang', 'Long', 'Nam',
        'Toàn', 'Tâm', 'Hưng', 'Duy', 'Tùng', 'Việt',
    ];

    // -------------------------------------------------------------------------
    // Tên chính – Nữ (hiện đại, tự nhiên)
    // -------------------------------------------------------------------------
    private static array $tenNu = [
        'Linh', 'Mai', 'Anh', 'Hà', 'Hương', 'Ngọc',
        'Nhung', 'Lan', 'My', 'Vy', 'Ngân', 'Nhi',
        'Phương', 'Trâm', 'Thư', 'Giang', 'Yến', 'Trang',
        'Châu', 'Dung', 'Hân', 'Ly', 'Tiên', 'Oanh',
    ];

    /**
     * Sinh tên Việt tự nhiên theo giới tính.
     * Xác suất ~60% có tên đệm cho giống thực tế.
     */
    private function randomVietnameseName(string $gender): string
    {
        $ho  = self::$ho[array_rand(self::$ho)];
        $hasDem = (rand(1, 10) <= 8); // 80% có tên đệm

        if ($gender === 'male') {
            $dem = $hasDem ? self::$demNam[array_rand(self::$demNam)] : null;
            $ten = self::$tenNam[array_rand(self::$tenNam)];
        } else {
            $dem = $hasDem ? self::$demNu[array_rand(self::$demNu)] : null;
            $ten = self::$tenNu[array_rand(self::$tenNu)];
        }

        return $dem ? "{$ho} {$dem} {$ten}" : "{$ho} {$ten}";
    }

    /**
     * Tạo 30 user giả để có dữ liệu tương tác.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('vi_VN');
        $avatarStyles = ['adventurer', 'avataaars', 'bottts', 'personas', 'pixel-art'];

        $accounts = [
            [
                'name' => 'Thông Nguyễn',
                'email' => 'thongnguyen.111004@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => \App\Enums\UserRole::ADMIN,
                'is_active' => true,
            ],
            [
                'name' => 'Thanh Nguyễn',
                'email' => 'holorblack@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => \App\Enums\UserRole::MODERATOR,
                'is_active' => true,
            ],
            [
                'name' => 'Tester Trần',
                'email' => 'recothelizard369@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => \App\Enums\UserRole::TESTER,
                'is_active' => true,
            ],
            [
                'name' => 'Thông Đức',
                'email' => 'thong.nd.64cntt@ntu.edu.vn',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => \App\Enums\UserRole::USER,
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            $style = $avatarStyles[array_rand($avatarStyles)];
            User::updateOrCreate(
                ['email' => $account['email']],
                array_merge($account, [
                    'avatar' => "https://api.dicebear.com/7.x/{$style}/svg?seed=" . urlencode($account['name']),
                ])
            );
        }

        // Random users
        for ($i = 1; $i <= 46; $i++) {
            $style = $avatarStyles[array_rand($avatarStyles)];
            $gender = $faker->randomElement(['male', 'female']);
            $name = $this->randomVietnameseName($gender);

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

        $this->command->info("✅ Tạo 4 accounts chính và 46 users ngẫu nhiên thành công!");
    }
}
