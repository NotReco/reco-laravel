<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\TvShow;
use App\Services\AdultContentDetectionService;
use Illuminate\Console\Command;

class AuditAdultRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reco:audit-adult-ratings 
                            {--apply-high : Tự động cập nhật age_rating = 18+ cho các mục high risk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit movie and TV show ratings to detect potentially misclassified adult content based on heuristics.';

    protected AdultContentDetectionService $detector;

    /**
     * Execute the console command.
     */
    public function handle(AdultContentDetectionService $detector)
    {
        $this->detector = $detector;
        $applyHigh = $this->option('apply-high');

        $this->info("🔍 Bắt đầu audit Adult Ratings...");
        if ($applyHigh) {
            $this->warn("⚠️ Chế độ --apply-high đang BẬT: Các mục High Risk sẽ bị cập nhật thành 18+!");
        }

        $this->auditMovies($applyHigh);
        $this->auditTvShows($applyHigh);

        $this->info("✅ Hoàn tất audit.");
        return self::SUCCESS;
    }

    protected function auditMovies(bool $applyHigh)
    {
        $this->info("\n🎬 Đang quét Movies...");
        $movies = Movie::with(['genres', 'tags'])->chunk(500, function ($movies) use ($applyHigh) {
            $tableData = [];
            
            foreach ($movies as $movie) {
                // Chỉ xét các phim chưa được đánh dấu là 18+
                if ($movie->isAdultRated()) {
                    continue;
                }

                $result = $this->detector->analyzeMovie($movie);

                if ($result['score'] >= 40) {
                    $tableData[] = [
                        $movie->id,
                        'Movie',
                        $movie->title,
                        $movie->age_rating ?: 'N/A',
                        $result['score'],
                        $result['risk_level'],
                        $result['suggested_age_rating'] ?: 'N/A',
                        implode(", ", $result['matched_signals'])
                    ];

                    if ($applyHigh && $result['risk_level'] === 'high') {
                        $movie->age_rating = '18+';
                        $movie->save();
                        $this->info("   [APPLIED] Đã cập nhật Movie ID {$movie->id} thành 18+");
                    }
                }
            }

            if (!empty($tableData)) {
                $this->table(
                    ['ID', 'Type', 'Title', 'Current Rating', 'Score', 'Risk', 'Suggested', 'Signals'],
                    $tableData
                );
            }
        });
    }

    protected function auditTvShows(bool $applyHigh)
    {
        $this->info("\n📺 Đang quét TV Shows...");
        $tvShows = TvShow::with(['genres', 'tags'])->chunk(500, function ($tvShows) use ($applyHigh) {
            $tableData = [];
            
            foreach ($tvShows as $tvShow) {
                // Chỉ xét các phim chưa được đánh dấu là 18+
                if ($tvShow->isAdultRated()) {
                    continue;
                }

                $result = $this->detector->analyzeTvShow($tvShow);

                if ($result['score'] >= 40) {
                    $tableData[] = [
                        $tvShow->id,
                        'TV Show',
                        $tvShow->title,
                        $tvShow->age_rating ?: 'N/A',
                        $result['score'],
                        $result['risk_level'],
                        $result['suggested_age_rating'] ?: 'N/A',
                        implode(", ", $result['matched_signals'])
                    ];

                    if ($applyHigh && $result['risk_level'] === 'high') {
                        $tvShow->age_rating = '18+';
                        $tvShow->save();
                        $this->info("   [APPLIED] Đã cập nhật TV Show ID {$tvShow->id} thành 18+");
                    }
                }
            }

            if (!empty($tableData)) {
                $this->table(
                    ['ID', 'Type', 'Title', 'Current Rating', 'Score', 'Risk', 'Suggested', 'Signals'],
                    $tableData
                );
            }
        });
    }
}
