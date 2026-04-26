<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Phim đang chiếu — mỗi 8 tiếng
Schedule::command('tmdb:import-movies --source=now_playing --pages=3')
    ->cron('0 */8 * * *')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_schedule.log'))
    ->name('tmdb:now_playing');

// 2. Phim phổ biến — mỗi 8 tiếng
Schedule::command('tmdb:import-movies --source=popular --pages=3')
    ->cron('0 */8 * * *')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_schedule.log'))
    ->name('tmdb:popular');

// 3. Phim sắp chiếu — mỗi ngày lúc 3:00 sáng
Schedule::command('tmdb:import-movies --source=upcoming --pages=3')
    ->dailyAt('03:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_schedule.log'))
    ->name('tmdb:upcoming');

// 4. Phim đánh giá cao — mỗi tuần vào thứ Hai lúc 3:00 sáng
Schedule::command('tmdb:import-movies --source=top_rated --pages=5')
    ->weeklyOn(1, '03:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_schedule.log'))
    ->name('tmdb:top_rated');

// 5. TV Series: Phim đang phát sóng hôm nay (airing_today) — mỗi 8 tiếng
Schedule::command('tmdb:import-tvshows --source=airing_today --pages=3')
    ->cron('0 */8 * * *')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_tv_schedule.log'))
    ->name('tmdb:tv_airing_today');

// 6. TV Series: Phẳng phát sóng trong tuần (on_the_air) — mỗi ngày lúc 4:00 sáng
Schedule::command('tmdb:import-tvshows --source=on_the_air --pages=3')
    ->dailyAt('04:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_tv_schedule.log'))
    ->name('tmdb:tv_on_the_air');

// 7. TV Series: Phổ biến (popular) — mỗi 8 tiếng
Schedule::command('tmdb:import-tvshows --source=popular --pages=3')
    ->cron('0 */8 * * *')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_tv_schedule.log'))
    ->name('tmdb:tv_popular');

// 8. TV Series: Đánh giá cao (top_rated) — mỗi tuần vào thứ Ba lúc 4:00 sáng
Schedule::command('tmdb:import-tvshows --source=top_rated --pages=5')
    ->weeklyOn(2, '04:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_tv_schedule.log'))
    ->name('tmdb:tv_top_rated');

// 9. Làm mới lại Carousel Trang chủ — mỗi ngày lúc 3:00 sáng
Schedule::command('carousel:auto-update')
    ->dailyAt('03:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/carousel_schedule.log'))
    ->name('carousel:auto');
