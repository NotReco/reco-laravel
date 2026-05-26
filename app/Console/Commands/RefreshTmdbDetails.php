<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\TvShow;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class RefreshTmdbDetails extends Command
{
    protected $signature = 'reco:refresh-tmdb-details';
    protected $description = 'Cập nhật lại toàn bộ data phim/TV từ TMDb';

    public function handle(TmdbService $tmdb)
    {
        $movieCmd = app(ImportTmdbMovies::class);
        $movieCmd->setTmdbService($tmdb);

        $movies = Movie::whereNotNull('tmdb_id')->get();
        $this->info("🎬 Đang làm mới {$movies->count()} Movies...");
        $bar = $this->output->createProgressBar($movies->count());
        $bar->start();

        foreach ($movies as $movie) {
            $movieCmd->importMovie($movie->tmdb_id);
            $bar->advance();
            usleep(100000); // 100ms
        }
        $bar->finish();
        $this->newLine(2);

        $tvCmd = app(ImportTmdbTvShows::class);
        $tvCmd->setTmdbService($tmdb);

        $tvShows = TvShow::whereNotNull('tmdb_id')->get();
        $this->info("📺 Đang làm mới {$tvShows->count()} TV Shows...");
        $barTv = $this->output->createProgressBar($tvShows->count());
        $barTv->start();

        foreach ($tvShows as $tv) {
            $tvCmd->importTvShow($tv->tmdb_id);
            $barTv->advance();
            usleep(100000); // 100ms
        }
        $barTv->finish();
        $this->newLine(2);

        $this->info("✅ Đã làm mới toàn bộ chi tiết phim/TV từ TMDb.");
    }
}
