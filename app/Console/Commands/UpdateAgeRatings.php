<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\TvShow;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\TmdbService;

class UpdateAgeRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reco:update-age-ratings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quickly update only age_rating for movies and tv shows from TMDb';

    /**
     * Execute the console command.
     */
    public function handle(TmdbService $tmdbService)
    {
        $this->info('Starting age_rating update...');

        // Update Movies
        $movies = Movie::whereNotNull('tmdb_id')->get();
        $this->info("Found {$movies->count()} movies to update.");
        $bar = $this->output->createProgressBar($movies->count());
        $bar->start();

        $moviesUpdated = 0;
        $apiKey = config('tmdb.api_key');
        $baseUrl = config('tmdb.base_url');

        foreach ($movies as $movie) {
            $response = Http::get("{$baseUrl}/movie/{$movie->tmdb_id}/release_dates", ['api_key' => $apiKey]);
            $data = $response->json();
            if ($response->successful() && isset($data['results'])) {
                $ratings = [];
                foreach ($data['results'] as $rd) {
                    $cert = null;
                    foreach ($rd['release_dates'] ?? [] as $r) {
                        if (!empty($r['certification'])) {
                            $cert = $r['certification'];
                            break;
                        }
                    }
                    if ($cert) {
                        $ratings[$rd['iso_3166_1']] = $cert;
                    }
                }

                $ageRating = null;
                if (isset($ratings['VN'])) {
                    $ageRating = $ratings['VN'];
                } elseif (isset($ratings['US'])) {
                    $ageRating = $ratings['US'];
                } elseif (!empty($ratings)) {
                    $ageRating = reset($ratings);
                }

                if ($ageRating !== null) {
                    $movie->update(['age_rating' => $ageRating]);
                    $moviesUpdated++;
                }
            }
            $bar->advance();
            usleep(50000); // 50ms delay to avoid rate limit
        }
        $bar->finish();
        $this->newLine();

        // Update TV Shows
        $tvShows = TvShow::whereNotNull('tmdb_id')->get();
        $this->info("Found {$tvShows->count()} TV shows to update.");
        $bar = $this->output->createProgressBar($tvShows->count());
        $bar->start();

        $tvUpdated = 0;
        foreach ($tvShows as $tvShow) {
            $response = Http::get("{$baseUrl}/tv/{$tvShow->tmdb_id}/content_ratings", ['api_key' => $apiKey]);
            $data = $response->json();
            if ($response->successful() && isset($data['results'])) {
                $ratings = [];
                foreach ($data['results'] as $cr) {
                    if (!empty($cr['rating'])) {
                        $ratings[$cr['iso_3166_1']] = $cr['rating'];
                    }
                }

                $ageRating = null;
                if (isset($ratings['VN'])) {
                    $ageRating = $ratings['VN'];
                } elseif (isset($ratings['US'])) {
                    $ageRating = $ratings['US'];
                } elseif (!empty($ratings)) {
                    $ageRating = reset($ratings);
                }

                if ($ageRating !== null) {
                    $tvShow->update(['age_rating' => $ageRating]);
                    $tvUpdated++;
                }
            }
            $bar->advance();
            usleep(50000); // 50ms delay
        }
        $bar->finish();
        $this->newLine();

        $this->info("Done! Updated $moviesUpdated movies and $tvUpdated TV shows.");
    }
}
