<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedDefaultAccounts();

        $this->call([
            UserSeeder::class,        // 30 user giả
            ReviewSeeder::class,      // Reviews + quick ratings
            InteractionSeeder::class, // Watchlist, likes, comments, follows
            ForumSeeder::class,       // Forum categories
        ]);
    }

    /**
     * Tạo tài khoản mặc định cho mỗi role.
     */
    protected function seedDefaultAccounts(): void
    {
        $accounts = [
            [
                'name' => 'Admin',
                'email' => 'thongnguyen.111004@gmail.com',
                'password' => '123456',
                'role' => UserRole::ADMIN,
            ],
            [
                'name' => 'Moderator',
                'email' => 'mod@gmail.com',
                'password' => '123456',
                'role' => UserRole::MODERATOR,
            ],
            [
                'name' => 'Tester',
                'email' => 'tester.test@gmail.com',
                'password' => '123456',
                'role' => UserRole::TESTER,
            ],
            [
                'name' => 'User',
                'email' => 'user@gmail.com',
                'password' => '123456',
                'role' => UserRole::USER,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
        }
    }
}
