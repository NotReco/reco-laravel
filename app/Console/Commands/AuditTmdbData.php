<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\TvShow;
use Illuminate\Console\Command;

class AuditTmdbData extends Command
{
    protected $signature = 'reco:audit-tmdb-data';
    protected $description = 'Kiểm tra data phim/TV sai lệch hoặc thiếu';

    public function handle()
    {
        $this->info("🎬 Đang quét Movies...");
        $movies = Movie::with(['genres', 'people', 'tags'])->get();
        $movieErrors = [];

        foreach ($movies as $movie) {
            $errors = [];
            if (empty($movie->runtime)) $errors[] = 'Thiếu runtime';
            if (empty($movie->country)) $errors[] = 'Thiếu country';
            if (empty($movie->language)) $errors[] = 'Thiếu language';
            if (empty($movie->age_rating)) $errors[] = 'Thiếu age_rating';
            if (empty($movie->trailer_url)) $errors[] = 'Thiếu trailer';
            if ($movie->genres->count() === 0) $errors[] = 'Thiếu genres';
            if ($movie->people->count() === 0) $errors[] = 'Thiếu cast/crew';
            
            $tags = $movie->tags->pluck('name')->toArray();
            if (in_array('black hole', $tags) || in_array('astronaut', $tags) || in_array('space travel', $tags)) {
                if (str_contains(strtolower($movie->title), 'titanic')) {
                    $errors[] = 'Keyword sai cho Titanic!';
                }
            }
            if (empty($tags)) {
                $errors[] = 'Thiếu keywords';
            }

            if (!empty($errors)) {
                $movieErrors[] = [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'type' => 'Movie',
                    'errors' => implode(', ', $errors),
                ];
            }
        }

        $this->info("📺 Đang quét TV Shows...");
        $tvShows = TvShow::with(['genres', 'people', 'tags'])->get();
        $tvErrors = [];

        foreach ($tvShows as $tv) {
            $errors = [];
            if (empty($tv->country)) $errors[] = 'Thiếu country';
            if (empty($tv->language)) $errors[] = 'Thiếu language';
            if (empty($tv->age_rating)) $errors[] = 'Thiếu age_rating';
            if (empty($tv->trailer_url)) $errors[] = 'Thiếu trailer';
            if ($tv->genres->count() === 0) $errors[] = 'Thiếu genres';
            if ($tv->people->count() === 0) $errors[] = 'Thiếu cast/crew';
            
            $tags = $tv->tags->pluck('name')->toArray();
            if (empty($tags)) {
                $errors[] = 'Thiếu keywords';
            }

            if (!empty($errors)) {
                $tvErrors[] = [
                    'id' => $tv->id,
                    'title' => $tv->title,
                    'type' => 'TV Show',
                    'errors' => implode(', ', $errors),
                ];
            }
        }

        if (empty($movieErrors) && empty($tvErrors)) {
            $this->info("✅ Tất cả dữ liệu đều hoàn hảo!");
        } else {
            $this->table(['ID', 'Title', 'Type', 'Errors'], array_merge($movieErrors, $tvErrors));
            $this->warn("Phát hiện " . (count($movieErrors) + count($tvErrors)) . " lỗi.");
        }
    }
}
