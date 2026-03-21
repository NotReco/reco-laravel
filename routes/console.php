<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ═══════════════════════════════════════════════════════════════
//  TMDB Auto-Import Schedule
//  Trên hosting Linux: thêm cron job sau vào cPanel / crontab:
//  * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
//  Trên Windows local: chạy `php artisan schedule:work` để test
// ═══════════════════════════════════════════════════════════════

// 1. Phim đang chiếu — mỗi ngày lúc 6:00 sáng
//    (Lịch chiếu rạp thay đổi hằng tuần, cần cập nhật thường xuyên)
Schedule::command('tmdb:import-movies --source=now_playing --pages=3')
    ->dailyAt('06:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_schedule.log'))
    ->name('tmdb:now_playing');

// 2. Phim phổ biến — mỗi ngày lúc 6:10 sáng
//    (Bảng xếp hạng popular thay đổi hằng ngày)
Schedule::command('tmdb:import-movies --source=popular --pages=3')
    ->dailyAt('06:10')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_schedule.log'))
    ->name('tmdb:popular');

// 3. Phim sắp chiếu — mỗi tuần vào thứ Hai lúc 7:00 sáng
//    (Lịch upcoming ít thay đổi, tuần 1 lần đủ)
Schedule::command('tmdb:import-movies --source=upcoming --pages=3')
    ->weeklyOn(1, '07:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_schedule.log'))
    ->name('tmdb:upcoming');

// 4. Phim đánh giá cao — mỗi tháng vào ngày 1 lúc 8:00 sáng
//    (Top rated thay đổi rất chậm, tháng 1 lần là đủ)
Schedule::command('tmdb:import-movies --source=top_rated --pages=5')
    ->monthlyOn(1, '08:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/tmdb_schedule.log'))
    ->name('tmdb:top_rated');
