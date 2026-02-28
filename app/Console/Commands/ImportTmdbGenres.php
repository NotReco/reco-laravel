<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class ImportTmdbGenres extends Command
{
    protected $signature = 'tmdb:import-genres';
    protected $description = 'Import thể loại phim từ TMDb API';

    public function handle(TmdbService $tmdb): int
    {
        $this->info('🎬 Đang lấy danh sách thể loại từ TMDb...');

        $genres = $tmdb->getGenres();

        if (!$genres) {
            $this->error('❌ Không thể lấy dữ liệu từ TMDb. Kiểm tra API key trong .env');
            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($genres));
        $bar->start();

        $created = 0;
        $updated = 0;

        foreach ($genres as $genreData) {
            $genre = Genre::withTrashed()->where('tmdb_id', $genreData['id'])->first();

            if ($genre) {
                $genre->update(['name' => $genreData['name']]);
                $updated++;
            } else {
                Genre::create([
                    'tmdb_id' => $genreData['id'],
                    'name' => $genreData['name'],
                ]);
                $created++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Hoàn tất! Tạo mới: {$created} | Cập nhật: {$updated}");

        return self::SUCCESS;
    }
}
