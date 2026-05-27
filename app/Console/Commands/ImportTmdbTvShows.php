<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\TvShow;
use App\Models\Person;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class ImportTmdbTvShows extends Command
{
    protected $signature = 'tmdb:import-tvshows
                            {--target=100 : Số lượng series mục tiêu cần import}
                            {--max-pages=50 : Số trang tối đa được quét để tránh lặp vô hạn}
                            {--source=mixed : Nguồn (mixed sẽ kết hợp nhiều nguồn)}';

    protected $description = 'Import TV series từ TMDb API (bao gồm cast & crew)';

    protected TmdbService $tmdb;
    protected int $created = 0;
    protected int $updated = 0;
    protected int $peopleCreated = 0;

    public function handle(TmdbService $tmdb): int
    {
        $this->tmdb = $tmdb;
        $target  = (int) $this->option('target');
        $maxPages = (int) $this->option('max-pages');
        $sourceOption = $this->option('source');

        $this->info("📺 Import TV series từ TMDb (Target: {$target}, Max Pages: {$maxPages}, Source: {$sourceOption})...");
        $this->newLine();

        $sources = $sourceOption === 'mixed' ? ['popular', 'top_rated'] : [$sourceOption];
        $curatedIds = [1399, 1396, 66732, 93405, 60625, 84958, 60059, 1402, 1416, 85271, 100088, 1424, 76479, 76331, 60574, 94997]; // Game of Thrones, Breaking Bad, Stranger Things, Squid Game, Rick and Morty, Loki, Better Call Saul...

        // 1. Import curated famous series first
        $this->info("🌟 Đang import Curated TV Shows...");
        foreach ($curatedIds as $tmdbId) {
            if ($this->created + $this->updated >= $target) break;
            $this->importTvShow($tmdbId);
        }

        // 2. Loop through sources
        foreach ($sources as $source) {
            if ($this->created + $this->updated >= $target) break;

            $this->info("📄 Lấy từ nguồn: {$source}");
            for ($page = 1; $page <= $maxPages; $page++) {
                if ($this->created + $this->updated >= $target) break;

                $this->info("   Trang {$page}");
                $data = match ($source) {
                    'top_rated' => $tmdb->getTopRatedTvShows($page),
                    'airing_today' => $tmdb->getAiringTodayTvShows($page),
                    'on_the_air' => $tmdb->getOnTheAirTvShows($page),
                    default => $tmdb->getPopularTvShows($page),
                };

                if (!$data || empty($data['results'])) {
                    $this->warn("⚠️  Không có dữ liệu trang {$page}");
                    break;
                }

                foreach ($data['results'] as $item) {
                    if ($this->created + $this->updated >= $target) break;
                    
                    // Filter: adult
                    if (($item['adult'] ?? false) === true) continue;
                    
                    // Filter: minimum quality
                    if (($item['vote_count'] ?? 0) < 300 || ($item['vote_average'] ?? 0) < 6.5 || ($item['popularity'] ?? 0) < 10) continue;
                    
                    // Filter: must have poster and backdrop
                    if (empty($item['poster_path']) || empty($item['backdrop_path'])) continue;
                    
                    // Filter: readability
                    $title = $item['name'] ?? $item['original_name'] ?? '';
                    if (preg_match('/[\p{Cyrillic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]/u', $title) && !in_array($item['original_language'] ?? '', ['en', 'vi'])) {
                        continue;
                    }

                    $this->importTvShow($item['id']);
                }

                // Rate limit
                usleep(250000);
            }
        }

        $this->newLine();
        $this->info('✅ Hoàn tất!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Series tạo mới', $this->created],
                ['Series cập nhật', $this->updated],
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
     * Import một TV series từ TMDb.
     */
    public function importTvShow(int $tmdbId): void
    {
        $detail = $this->tmdb->getTvShowDetail($tmdbId);
        if (!$detail) return;

        // Parse networks: lưu id, name, logo
        $networks = array_map(fn($n) => [
            'id'        => $n['id'],
            'name'      => $n['name'],
            'logo_path' => $n['logo_path'] ?? null,
            'logo_url'  => $n['logo_path']
                ? $this->tmdb->posterUrl($n['logo_path'], 'small')
                : null,
        ], $detail['networks'] ?? []);

        // Xử lý age rating từ content_ratings (ưu tiên VN > US > đầu tiên có rating)
        $ageRating = null;
        if (isset($detail['content_ratings']['results'])) {
            $ratings = [];
            foreach ($detail['content_ratings']['results'] as $rd) {
                if (!empty($rd['rating'])) {
                    $ratings[$rd['iso_3166_1']] = $rd['rating'];
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

        // Fallback: nếu không có rating nhưng adult=true thì bắt buộc là 18+
        if (empty($ageRating) && ($detail['adult'] ?? false) === true) {
            $ageRating = '18+';
        }

        $attributes = [
            'tmdb_id'            => $tmdbId,
            'title'              => $detail['name'] ?? $detail['original_name'] ?? 'Unknown',
            'original_title'     => $detail['original_name'] ?? null,
            'tagline'            => $detail['tagline'] ?? null,
            'synopsis'           => $detail['overview'] ?? null,
            'poster'             => $this->tmdb->posterUrl($detail['poster_path'] ?? null, 'large'),
            'backdrop'           => $this->tmdb->backdropUrl($detail['backdrop_path'] ?? null, 'large'),
            'first_air_date'     => $detail['first_air_date'] ?: null,
            'last_air_date'      => $detail['last_air_date'] ?: null,
            'number_of_seasons'  => $detail['number_of_seasons'] ?? null,
            'number_of_episodes' => $detail['number_of_episodes'] ?? null,
            'episode_runtime'    => isset($detail['episode_run_time'][0]) ? $detail['episode_run_time'][0] : null,
            'networks'           => $networks ?: null,
            'type'               => $detail['type'] ?? null,
            'tmdb_status'        => $detail['status'] ?? null,
            'country'            => $detail['origin_country'][0] ?? null,
            'language'           => $detail['original_language'] ?? null,
            'is_approved'        => true,
            'status'             => 'active',
            'age_rating'         => $ageRating,
        ];

        // Trailer từ videos
        foreach ($detail['videos']['results'] ?? [] as $video) {
            if ($video['site'] === 'YouTube' && in_array($video['type'], ['Trailer', 'Teaser'])) {
                $attributes['trailer_url'] = "https://www.youtube.com/watch?v={$video['key']}";
                break;
            }
        }

        $show = TvShow::withTrashed()->where('tmdb_id', $tmdbId)->first();

        if ($show) {
            $show->update($attributes);
            $this->updated++;
        } else {
            $show = TvShow::create($attributes);
            $this->created++;
        }

        $this->syncGenres($show, $detail['genres'] ?? []);
        $this->syncCredits($show, $detail['credits'] ?? []);
        $this->syncKeywords($show, $detail['keywords'] ?? []);
    }

    /**
     * Đồng bộ từ khóa (keywords).
     */
    protected function syncKeywords(TvShow $show, array $keywordsData): void
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
        $show->tags()->sync($tagIds);
    }

    /**
     * Đồng bộ thể loại (dùng chung bảng genres với Movie).
     */
    protected function syncGenres(TvShow $show, array $genres): void
    {
        $genreIds = [];

        foreach ($genres as $genreData) {
            $genre = Genre::withTrashed()->where('tmdb_id', $genreData['id'])->first();
            if (!$genre) {
                $genre = Genre::create([
                    'tmdb_id' => $genreData['id'],
                    'name'    => $genreData['name']
                ]);
            }
            $genreIds[] = $genre->id;
        }

        $show->genres()->sync($genreIds);
    }

    /**
     * Đồng bộ diễn viên & đoàn làm phim.
     */
    protected function syncCredits(TvShow $show, array $credits): void
    {
        $syncData = [];

        // Cast — top 10
        foreach (array_slice($credits['cast'] ?? [], 0, 10) as $index => $member) {
            $person = $this->findOrCreatePerson($member);
            if ($person) {
                $syncData[$person->id] = [
                    'role'           => 'actor',
                    'character_name' => $member['character'] ?? null,
                    'display_order'  => $index,
                ];
            }
        }

        // Crew
        $importRoles = [
            'Director'   => 'director',
            'Writer'     => 'writer',
            'Screenplay' => 'writer',
            'Producer'   => 'producer',
        ];

        foreach ($credits['crew'] ?? [] as $member) {
            $job = $member['job'] ?? '';
            if (!isset($importRoles[$job])) continue;

            $person = $this->findOrCreatePerson($member);
            if ($person && !isset($syncData[$person->id])) {
                $syncData[$person->id] = [
                    'role'           => $importRoles[$job],
                    'character_name' => null,
                    'display_order'  => 0,
                ];
            }
        }

        $show->people()->sync($syncData);
    }

    /**
     * Tìm hoặc tạo nghệ sĩ (dùng chung bảng people với Movie).
     */
    protected function findOrCreatePerson(array $data): ?Person
    {
        $tmdbId = $data['id'] ?? null;
        if (!$tmdbId) return null;

        $person = Person::withTrashed()->where('tmdb_id', $tmdbId)->first();

        if (!$person) {
            $person = Person::create([
                'tmdb_id'  => $tmdbId,
                'name'     => $data['name'] ?? 'Unknown',
                'photo'    => $this->tmdb->profileUrl($data['profile_path'] ?? null, 'medium'),
                'known_for' => $data['known_for_department'] ?? null,
            ]);
            $this->peopleCreated++;
        }

        return $person;
    }

    /**
     * Map TMDB US content rating sang age rating nội bộ của hệ thống.
     */
    protected function normalizeCertification(string $cert): string
    {
        return match (strtoupper(trim($cert))) {
            'NC-17', 'R', 'TV-MA', '18+', 'A' => '18+',
            'PG-13', 'TV-14', 'U/A 13+' => 'T13',
            'TV-15', '15', 'U/A 16+', '16+' => 'T16',
            'G', 'PG', 'TV-G', 'TV-Y', 'TV-PG', 'U' => 'P',
            'NR', 'N/A', '', 'NULL' => 'Chưa phân loại',
            default => 'Chưa phân loại',
        };
    }
}
