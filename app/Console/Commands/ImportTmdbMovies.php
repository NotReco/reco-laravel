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
                            {--target=120 : Số lượng phim mục tiêu cần import}
                            {--max-pages=50 : Số trang tối đa được quét để tránh lặp vô hạn}
                            {--source=mixed : Nguồn phim (mixed sẽ kết hợp nhiều nguồn)}';

    protected $description = 'Import phim từ TMDb API (bao gồm cast & crew)';

    protected TmdbService $tmdb;
    protected int $moviesCreated = 0;
    protected int $moviesUpdated = 0;
    protected int $peopleCreated = 0;

    public function handle(TmdbService $tmdb): int
    {
        $this->tmdb = $tmdb;
        $target = (int) $this->option('target');
        $maxPages = (int) $this->option('max-pages');
        $sourceOption = $this->option('source');

        $this->info("🎬 Import phim từ TMDb (Target: {$target}, Max Pages: {$maxPages}, Source: {$sourceOption})...");
        $this->newLine();

        $sources = $sourceOption === 'mixed' ? ['popular', 'top_rated', 'discover'] : [$sourceOption];
        $curatedIds = [278, 238, 155, 13, 122, 680, 550, 157336, 11, 603, 155, 27205, 597, 109445, 1726, 101, 769, 510, 24428];

        // 1. Import curated famous movies first
        $this->info("🌟 Đang import Curated Movies...");
        foreach ($curatedIds as $tmdbId) {
            if ($this->moviesCreated + $this->moviesUpdated >= $target) break;
            $this->importMovie($tmdbId);
        }

        // 2. Loop through sources
        foreach ($sources as $source) {
            if ($this->moviesCreated + $this->moviesUpdated >= $target) break;

            $this->info("📄 Lấy từ nguồn: {$source}");
            for ($page = 1; $page <= $maxPages; $page++) {
                if ($this->moviesCreated + $this->moviesUpdated >= $target) break;

                $this->info("   Trang {$page}");
                $data = match ($source) {
                    'top_rated' => $tmdb->getTopRatedMovies($page),
                    'discover' => $tmdb->discoverMovies(['vote_count.gte' => 500, 'vote_average.gte' => 7.0], $page),
                    default => $tmdb->getPopularMovies($page),
                };

                if (!$data || empty($data['results'])) {
                    $this->warn("⚠️  Không có dữ liệu trang {$page}");
                    break;
                }

                foreach ($data['results'] as $movieData) {
                    if ($this->moviesCreated + $this->moviesUpdated >= $target) break;
                    
                    // Filter: adult
                    if (($movieData['adult'] ?? false) === true) continue;
                    
                    // Filter: minimum quality (vote & popularity)
                    if (($movieData['vote_count'] ?? 0) < 500 || ($movieData['vote_average'] ?? 0) < 6.5 || ($movieData['popularity'] ?? 0) < 10) continue;
                    
                    // Filter: must have poster and backdrop
                    if (empty($movieData['poster_path']) || empty($movieData['backdrop_path'])) continue;
                    
                    // Filter: readability
                    $title = $movieData['title'] ?? $movieData['original_title'] ?? '';
                    if (preg_match('/[\p{Cyrillic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]/u', $title) && !in_array($movieData['original_language'] ?? '', ['en', 'vi'])) {
                        // try to skip if title still contains strange chars
                        continue;
                    }

                    $this->importMovie($movieData['id']);
                }

                // Rate limit
                usleep(250000);
            }
        }

        $this->newLine();
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

    public function setTmdbService(TmdbService $tmdb): void
    {
        $this->tmdb = $tmdb;
    }

    /**
     * Import một phim từ TMDb (bao gồm chi tiết + cast/crew).
     */
    public function importMovie(int $tmdbId): void
    {
        $detail = $this->tmdb->getMovieDetail($tmdbId);
        if (!$detail)
            return;

        // Tạo hoặc cập nhật phim
        $movie = Movie::withTrashed()->where('tmdb_id', $tmdbId)->first();

        // Xử lý age rating từ release_dates (ưu tiên VN > US > đầu tiên có rating)
        $ageRating = null;
        if (isset($detail['release_dates']['results'])) {
            $ratings = [];
            foreach ($detail['release_dates']['results'] as $rd) {
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

            if (isset($ratings['VN'])) {
                $ageRating = $ratings['VN'];
            } elseif (isset($ratings['US'])) {
                $ageRating = $this->normalizeCertification($ratings['US']);
            } elseif (!empty($ratings)) {
                $ageRating = reset($ratings);
            }
        }

        // Fallback: nếu không có certification nhưng adult=true thì bắt buộc là 18+
        if (empty($ageRating) && ($detail['adult'] ?? false) === true) {
            $ageRating = '18+';
        }

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
            'age_rating' => $ageRating,
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
            $genre = Genre::withTrashed()->where('tmdb_id', $genreData['id'])->first();

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

            try {
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
            } catch (\Illuminate\Database\QueryException $e) {
                // Nếu bị duplicate thì fetch lại từ db
                $person = Person::withTrashed()->where('tmdb_id', $tmdbId)->first();
            }

            // Rate limit nhẹ để tránh vượt quota
            usleep(100000); // 100ms
        }

        return $person;
    }

    /**
     * Map TMDB US certification sang age rating nội bộ của hệ thống.
     * Chỉ áp dụng khi không có VN certification.
     */
    protected function normalizeCertification(string $cert): string
    {
        return match (strtoupper(trim($cert))) {
            'NC-17', 'R', 'TV-MA', '18+', 'A' => '18+',
            'PG-13', 'TV-14', 'U/A 13+' => 'T13',
            'TV-15', '15', 'U/A 16+', '16+' => 'T16',
            'G', 'PG', 'TV-G', 'TV-Y', 'TV-PG', 'U' => 'P',
            'NR', 'N/A', '', 'NULL' => 'Chưa phân loại',
            default => 'Chưa phân loại', // Nếu không map được thì đưa về Chưa phân loại thay vì giữ nguyên raw
        };
    }
}
