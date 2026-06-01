<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            BannedWordSeeder::class,
        ]);

        $this->command->info('Đang import TMDB Genres...');
        \Illuminate\Support\Facades\Artisan::call('tmdb:import-genres');
        $this->command->info(\Illuminate\Support\Facades\Artisan::output());

        $this->command->info('Đang import TMDB Movies (target: 120)...');
        \Illuminate\Support\Facades\Artisan::call('tmdb:import-movies', [
            '--target' => 120,
        ]);
        $this->command->info(\Illuminate\Support\Facades\Artisan::output());

        $this->command->info('Đang import TMDB TV Shows (target: 100)...');
        \Illuminate\Support\Facades\Artisan::call('tmdb:import-tvshows', [
            '--target' => 100,
        ]);
        $this->command->info(\Illuminate\Support\Facades\Artisan::output());

        $this->call([
            ReviewSeeder::class,
            InteractionSeeder::class,
            ForumSeeder::class,
            UserRewardSeeder::class,
        ]);
    }
}
