<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PopulateDemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $fixedAccounts = [
            [
                'name' => 'Thong Nguyen',
                'email' => 'thongnguyen.111004@gmail.com',
                'role' => UserRole::ADMIN,
            ],
            [
                'name' => 'Mod Nguyen',
                'email' => 'mod@gmail.com',
                'role' => UserRole::MODERATOR,
            ],
            [
                'name' => 'Tester Tran',
                'email' => 'tester.test@gmail.com',
                'role' => UserRole::TESTER,
            ],
            [
                'name' => 'User Le',
                'email' => 'user@gmail.com',
                'role' => UserRole::USER,
            ],
        ];

        foreach ($fixedAccounts as $account) {
            User::withTrashed()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => '123456',
                    'role' => $account['role'],
                    'is_active' => true,
                    'email_verified_at' => $now,
                    'deleted_at' => null,
                ]
            );
        }

        $fixedEmails = array_map(static fn(array $acc) => $acc['email'], $fixedAccounts);

        // Dọn bộ user mẫu cũ để luôn giữ đúng ~50 tài khoản demo theo format mới.
        User::query()->whereNotIn('email', $fixedEmails)->delete();

        $faker = \Faker\Factory::create('vi_VN');
        $cities = [
            'TP. Ho Chi Minh',
            'Ha Noi',
            'Da Nang',
            'Can Tho',
            'Hai Phong',
            'Nha Trang',
            'Hue',
            'Da Lat',
            'Vung Tau',
            'Quy Nhon',
            'Bien Hoa',
            'Thu Duc',
        ];
        $domains = ['gmail.com', 'outlook.com', 'yahoo.com', 'icloud.com'];
        $bios = [
            'Me phim tam ly toi pham va phim Han Quoc.',
            'Cuoi tuan thuong xem phim cung gia dinh.',
            'Fan phim hoat hinh va phim phieu luu.',
            'Thich review ngan gon va de hieu.',
            'Nghien phim co dien va sci-fi.',
            'Hay canh me phim hot moi tuan.',
            'Muc tieu 300 phim moi nam.',
            'Vua xem phim vua ghi chu cam nhan.',
        ];
        $lastNames = ['Nguyen', 'Tran', 'Le', 'Pham', 'Hoang', 'Phan', 'Vu', 'Dang', 'Bui', 'Do'];
        $middleNames = ['Minh', 'Gia', 'Thanh', 'Anh', 'Quoc', 'Tuan', 'Thi', 'Thu', 'Ngoc', 'Bao'];
        $firstNames = ['An', 'Binh', 'Chi', 'Dung', 'Hieu', 'Khanh', 'Linh', 'Nam', 'Phuong', 'Trang', 'Vy', 'Son'];
        $usedEmails = [];

        for ($i = 1; $i <= 46; $i++) {
            $name = sprintf(
                '%s %s %s',
                $lastNames[array_rand($lastNames)],
                $middleNames[array_rand($middleNames)],
                $firstNames[array_rand($firstNames)]
            );

            $base = (string) Str::of($name)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '.')->trim('.');
            $email = sprintf('%s.%02d@%s', $base, $i, $domains[array_rand($domains)]);
            while (isset($usedEmails[$email])) {
                $email = sprintf('%s.%02d.%d@%s', $base, $i, random_int(10, 99), $domains[array_rand($domains)]);
            }
            $usedEmails[$email] = true;

            $birthYear = random_int(1987, 2007);
            $birthDate = Carbon::create(
                $birthYear,
                random_int(1, 12),
                random_int(1, 28)
            )->format('Y-m-d');

            User::withTrashed()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => '123456',
                    'role' => UserRole::USER,
                    'is_active' => true,
                    'email_verified_at' => $now,
                    'gender' => $faker->randomElement(['male', 'female']),
                    'location' => $cities[array_rand($cities)],
                    'bio' => $bios[array_rand($bios)],
                    'date_of_birth' => $birthDate,
                    'avatar' => 'https://api.dicebear.com/7.x/personas/svg?seed=' . urlencode($name),
                    'deleted_at' => null,
                ]
            );
        }

        $this->command?->info('Seeded 50 demo users (VN style) successfully.');
    }
}

