<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Enums\UserRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    // -----------------------------------------------------------------------
    // Họ phổ biến tại Việt Nam
    // -----------------------------------------------------------------------
    private static array $ho = [
        'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh',
        'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ',
        'Hồ', 'Ngô', 'Dương', 'Lý',
    ];

    private static array $demNam = [
        'Minh', 'Hải', 'Quang', 'Đức', 'Tuấn', 'Gia',
        'Nhật', 'Thái', 'Trọng', 'Phúc', 'Khoa', 'Anh',
        'Hùng', 'Tiến', 'Hoàng', 'Công',
    ];

    private static array $demNu = [
        'Thanh', 'Thu', 'Ngọc', 'Mai', 'Thị', 'Bảo',
        'Diễm', 'Hồng', 'Trúc', 'Phương', 'Khánh', 'Quỳnh',
        'Tuyết', 'Uyên', 'Nhã', 'Bích',
    ];

    private static array $tenNam = [
        'Đăng', 'Trí', 'Bách', 'Huy', 'Anh', 'Kiệt',
        'Bảo', 'Nghĩa', 'Khôi', 'Phong', 'Sơn', 'Lâm',
        'Hải', 'Dũng', 'Quân', 'Khang', 'Long', 'Nam',
        'Toàn', 'Tâm', 'Hưng', 'Duy', 'Tùng', 'Việt',
    ];

    private static array $tenNu = [
        'Linh', 'Mai', 'Anh', 'Hà', 'Hương', 'Ngọc',
        'Nhung', 'Lan', 'My', 'Vy', 'Ngân', 'Nhi',
        'Phương', 'Trâm', 'Thư', 'Giang', 'Yến', 'Trang',
        'Châu', 'Dung', 'Hân', 'Ly', 'Tiên', 'Oanh',
    ];

    /** Sinh tên Việt tự nhiên theo giới tính. */
    private function randomVietnameseName(string $gender): string
    {
        $ho = self::$ho[array_rand(self::$ho)];
        $hasDem = (rand(1, 10) <= 8);

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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        return [
            'name'               => $this->randomVietnameseName($gender),
            'email'              => fake()->unique()->safeEmail(),
            'email_verified_at'  => now(),
            'password'           => static::$password ??= Hash::make('password'),
            'remember_token'     => Str::random(10),
            'role'               => UserRole::USER,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
