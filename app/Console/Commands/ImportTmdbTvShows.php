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
                            {--pages=3 : Số trang cần lấy (mỗi trang 20 series)}
                            {--source=popular : Nguồn: popular, top_rated, airing_today, on_the_air}';

    protected $description = 'Import TV series từ TMDb API (bao gồm cast & crew)';

    protected TmdbService $tmdb;
    protected int $created = 0;
    protected int $updated = 0;
    protected int $peopleCreated = 0;

    public function handle(TmdbService $tmdb): int
    {
        $this->tmdb = $tmdb;
        $pages  = (int) $this->option('pages');
        $source = $this->option('source');

        $this->info("📺 Import TV series từ TMDb ({$source}) — {$pages} trang...");
        $this->newLine();

        for ($page = 1; $page <= $pages; $page++) {
            $this->info("📄 Trang {$page}/{$pages}");

            $data = match ($source) {
                'top_rated'    => $tmdb->getTopRatedTvShows($page),
                'airing_today' => $tmdb->getAiringTodayTvShows($page),
                'on_the_air'   => $tmdb->getOnTheAirTvShows($page),
                default        => $tmdb->getPopularTvShows($page),
            };

            if (!$data || empty($data['results'])) {
                $this->warn("⚠️  Không có dữ liệu trang {$page}");
                continue;
            }

            $bar = $this->output->createProgressBar(count($data['results']));
            $bar->start();

            foreach ($data['results'] as $item) {
                $this->importTvShow($item['id']);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            if ($page < $pages) {
                usleep(250000); // 250ms rate limit
            }
        }

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

    /**
     * Import một TV series từ TMDb.
     */
    protected function importTvShow(int $tmdbId): void
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
            $genre = Genre::firstOrCreate(
                ['tmdb_id' => $genreData['id']],
                ['name'    => $genreData['name']]
            );
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
}
