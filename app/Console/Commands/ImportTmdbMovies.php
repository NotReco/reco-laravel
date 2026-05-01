<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Person;
use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportTmdbMovies extends Command
{
    protected $signature = 'tmdb:import-movies
                            {--pages=3 : Số trang cần lấy (mỗi trang 20 phim)}
                            {--source=popular : Nguồn phim: popular, top_rated, now_playing, upcoming}';

    protected $description = 'Import phim từ TMDb API (bao gồm cast & crew)';

    protected TmdbService $tmdb;
    protected int $moviesCreated = 0;
    protected int $moviesUpdated = 0;
    protected int $peopleCreated = 0;

    public function handle(TmdbService $tmdb): int
    {
        $this->tmdb = $tmdb;
        $pages = (int) $this->option('pages');
        $source = $this->option('source');

        $this->info("🎬 Import phim từ TMDb ({$source}) — {$pages} trang...");
        $this->newLine();

        for ($page = 1; $page <= $pages; $page++) {
            $this->info("📄 Trang {$page}/{$pages}");

            $data = match ($source) {
                'top_rated' => $tmdb->getTopRatedMovies($page),
                'now_playing' => $tmdb->getNowPlayingMovies($page),
                'upcoming' => $tmdb->getUpcomingMovies($page),
                default => $tmdb->getPopularMovies($page),
            };

            if (!$data || empty($data['results'])) {
                $this->warn("⚠️  Không có dữ liệu trang {$page}");
                continue;
            }

            $bar = $this->output->createProgressBar(count($data['results']));
            $bar->start();

            foreach ($data['results'] as $movieData) {
                $this->importMovie($movieData['id']);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            // Rate limit: đợi 250ms giữa các trang
            if ($page < $pages) {
                usleep(250000);
            }
        }

        $this->info("✅ Hoàn tất!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Phim tạo mới', $this->moviesCreated],
                ['Phim cập nhật', $this->moviesUpdated],
                ['Nghệ sĩ tạo mới', $this->peopleCreated],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Import một phim từ TMDb (bao gồm chi tiết + cast/crew).
     */
    protected function importMovie(int $tmdbId): void
    {
        $detail = $this->tmdb->getMovieDetail($tmdbId);
        if (!$detail)
            return;

        // Tạo hoặc cập nhật phim
        $movie = Movie::withTrashed()->where('tmdb_id', $tmdbId)->first();

        $movieAttributes = [
            'tmdb_id' => $tmdbId,
            'title' => $detail['title'] ?? $detail['original_title'] ?? 'Unknown',
            'original_title' => $detail['original_title'] ?? null,
            'tagline' => $detail['tagline'] ?? null,
            'synopsis' => $detail['overview'] ?? null,
            'poster' => $this->tmdb->posterUrl($detail['poster_path'] ?? null, 'large'),
            'backdrop' => $this->tmdb->backdropUrl($detail['backdrop_path'] ?? null, 'large'),
            'release_date' => $detail['release_date'] ?: null,
            'runtime' => $detail['runtime'] ?? null,
            'country' => isset($detail['production_countries'][0]) ? $detail['production_countries'][0]['iso_3166_1'] : null,
            'language' => $detail['original_language'] ?? null,
            'budget' => $detail['budget'] ?? null,
            'revenue' => $detail['revenue'] ?? null,
            'is_approved' => true,
            'status' => 'active',
        ];

        // Xử lý trailer YouTube từ videos
        if (isset($detail['videos']['results'])) {
            foreach ($detail['videos']['results'] as $video) {
                if ($video['site'] === 'YouTube' && in_array($video['type'], ['Trailer', 'Teaser'])) {
                    $movieAttributes['trailer_url'] = "https://www.youtube.com/watch?v={$video['key']}";
                    break;
                }
            }
        }

        if ($movie) {
            $movie->update($movieAttributes);
            $this->moviesUpdated++;
        } else {
            $movie = Movie::create($movieAttributes);
            $this->moviesCreated++;
        }

        // Sync genres
        $this->syncGenres($movie, $detail['genres'] ?? []);

        // Sync cast & crew
        $this->syncCredits($movie, $detail['credits'] ?? []);

        // Sync keywords
        $this->syncKeywords($movie, $detail['keywords'] ?? []);
    }

    /**
     * Đồng bộ từ khóa (keywords).
     */
    protected function syncKeywords(Movie $movie, array $keywordsData): void
    {
        $rawKeywords = $keywordsData['results'] ?? $keywordsData['keywords'] ?? [];
        $tagIds = [];

        foreach ($rawKeywords as $kw) {
            if (isset($kw['name'])) {
                $tag = \App\Models\Tag::firstOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($kw['name'])],
                    ['name' => strtolower($kw['name'])]
                );
                $tagIds[] = $tag->id;
            }
        }
        $movie->tags()->sync($tagIds);
    }

    /**
     * Đồng bộ thể loại phim.
     */
    protected function syncGenres(Movie $movie, array $genres): void
    {
        $genreIds = [];

        foreach ($genres as $genreData) {
            $genre = Genre::where('tmdb_id', $genreData['id'])->first();

            if (!$genre) {
                $genre = Genre::create([
                    'tmdb_id' => $genreData['id'],
                    'name' => $genreData['name'],
                ]);
            }

            $genreIds[] = $genre->id;
        }

        $movie->genres()->sync($genreIds);
    }

    /**
     * Đồng bộ diễn viên & đoàn làm phim.
     */
    protected function syncCredits(Movie $movie, array $credits): void
    {
        $syncData = [];

        // Cast — lấy top 10 diễn viên
        $cast = array_slice($credits['cast'] ?? [], 0, 10);
        foreach ($cast as $index => $castMember) {
            $person = $this->findOrCreatePerson($castMember);
            if ($person) {
                $syncData[$person->id] = [
                    'role' => 'actor',
                    'character_name' => $castMember['character'] ?? null,
                    'display_order' => $index,
                ];
            }
        }

        // Crew — chỉ lấy Director, Writer, Producer
        $importRoles = [
            'Director' => 'director',
            'Writer' => 'writer',
            'Screenplay' => 'writer',
            'Producer' => 'producer',
        ];

        foreach ($credits['crew'] ?? [] as $crewMember) {
            $job = $crewMember['job'] ?? '';
            if (!isset($importRoles[$job]))
                continue;

            $person = $this->findOrCreatePerson($crewMember);
            if ($person && !isset($syncData[$person->id])) {
                $syncData[$person->id] = [
                    'role' => $importRoles[$job],
                    'character_name' => null,
                    'display_order' => 0,
                ];
            }
        }

        $movie->people()->sync($syncData);
    }

    /**
     * Tìm hoặc tạo mới nghệ sĩ từ TMDb data.
     * Khi tạo mới, tự động fetch thêm thông tin chi tiết (bio, giới tính, nơi sinh, external IDs).
     */
    protected function findOrCreatePerson(array $data): ?Person
    {
        $tmdbId = $data['id'] ?? null;
        if (!$tmdbId)
            return null;

        $person = Person::withTrashed()->where('tmdb_id', $tmdbId)->first();

        if (!$person) {
            // Lấy chi tiết đầy đủ từ TMDb (có external_ids)
            $detail = $this->tmdb->getPersonDetail($tmdbId);
            $ext = $detail['external_ids'] ?? [];

            $aliases = collect($detail['also_known_as'] ?? [])
                ->filter(fn($a) => $a !== ($detail['name'] ?? ''))
                ->values()->all();

            $person = Person::create([
                'tmdb_id'        => $tmdbId,
                'name'           => $detail['name'] ?? $data['name'] ?? 'Unknown',
                'photo'          => $this->tmdb->profileUrl($detail['profile_path'] ?? $data['profile_path'] ?? null, 'large'),
                'biography'      => $detail['biography'] ?? null,
                'bio'            => $detail['biography'] ?? null,
                'known_for'      => $detail['known_for_department'] ?? $data['known_for_department'] ?? null,
                'gender'         => $detail['gender'] ?? 0,
                'place_of_birth' => $detail['place_of_birth'] ?? null,
                'also_known_as'  => !empty($aliases) ? $aliases : null,
                'homepage'       => !empty($detail['homepage']) ? $detail['homepage'] : null,
                'imdb_id'        => $ext['imdb_id'] ?? null,
                'instagram_id'   => $ext['instagram_id'] ?? null,
                'twitter_id'     => $ext['twitter_id'] ?? null,
                'date_of_birth'  => !empty($detail['birthday']) ? $detail['birthday'] : null,
                'date_of_death'  => !empty($detail['deathday']) ? $detail['deathday'] : null,
                'nationality'    => isset($detail['place_of_birth']) && str_contains($detail['place_of_birth'], ',')
                    ? trim(substr($detail['place_of_birth'], strrpos($detail['place_of_birth'], ',') + 1))
                    : null,
            ]);
            $this->peopleCreated++;

            // Rate limit nhẹ để tránh vượt quota
            usleep(100000); // 100ms
        }

        return $person;
    }
}
