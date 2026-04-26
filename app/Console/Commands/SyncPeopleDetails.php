<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class SyncPeopleDetails extends Command
{
    protected $signature = 'tmdb:sync-people
                            {--limit=0 : Giới hạn số người cần sync (0 = tất cả)}
                            {--missing : Chỉ sync người chưa có thông tin đầy đủ}
                            {--id= : Sync một người cụ thể theo TMDB ID}';

    protected $description = 'Đồng bộ thông tin chi tiết diễn viên/đạo diễn từ TMDb (tiểu sử, giới tính, nơi sinh, mạng xã hội...)';

    protected TmdbService $tmdb;
    protected int $updated = 0;
    protected int $skipped = 0;
    protected int $failed  = 0;

    public function handle(TmdbService $tmdb): int
    {
        $this->tmdb = $tmdb;

        // ── Sync một người cụ thể ──────────────────────────────────
        if ($tmdbId = $this->option('id')) {
            $person = Person::where('tmdb_id', $tmdbId)->first();
            if (!$person) {
                $this->error("Không tìm thấy người có TMDB ID: {$tmdbId}");
                return self::FAILURE;
            }
            $this->syncPerson($person);
            $this->info("✅ Đã sync: {$person->name}");
            return self::SUCCESS;
        }

        // ── Lọc người cần sync ─────────────────────────────────────
        $query = Person::whereNotNull('tmdb_id');

        if ($this->option('missing')) {
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNull('biography')->orWhere('biography', '');
                })
                ->orWhereNull('gender')
                ->orWhereNull('place_of_birth');
            });
        }

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $total = $query->count();
        $this->info("🎭 Bắt đầu sync chi tiết {$total} người từ TMDb...");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->orderBy('id')->chunk(50, function ($people) use ($bar) {
            foreach ($people as $person) {
                $this->syncPerson($person);
                $bar->advance();
                // Rate limit: 4 req/s
                usleep(250000);
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Kết quả', 'Số lượng'],
            [
                ['✅ Đã cập nhật', $this->updated],
                ['⏭️  Bỏ qua',     $this->skipped],
                ['❌ Lỗi',         $this->failed],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Sync chi tiết một người từ TMDb API.
     */
    protected function syncPerson(Person $person): void
    {
        if (!$person->tmdb_id) {
            $this->skipped++;
            return;
        }

        $data = $this->tmdb->getPersonDetail($person->tmdb_id);

        if (!$data) {
            $this->failed++;
            return;
        }

        // External IDs (instagram, twitter, imdb)
        $ext = $data['external_ids'] ?? [];

        // also_known_as: loại bỏ trùng với tên chính
        $aliases = collect($data['also_known_as'] ?? [])
            ->filter(fn($a) => $a !== $person->name)
            ->values()
            ->all();

        $attributes = [
            'name'          => $data['name'] ?? $person->name,
            'biography'     => !empty($data['biography']) ? $data['biography'] : $person->biography,
            'gender'        => $data['gender'] ?? $person->gender ?? 0,
            'place_of_birth'=> $data['place_of_birth'] ?? $person->place_of_birth,
            'also_known_as' => !empty($aliases) ? $aliases : $person->also_known_as,
            'homepage'      => !empty($data['homepage']) ? $data['homepage'] : $person->homepage,
            'imdb_id'       => $ext['imdb_id']      ?? $person->imdb_id,
            'instagram_id'  => $ext['instagram_id'] ?? $person->instagram_id,
            'twitter_id'    => $ext['twitter_id']   ?? $person->twitter_id,
        ];

        // Cập nhật photo nếu chưa có
        if (!$person->photo && !empty($data['profile_path'])) {
            $attributes['photo'] = $this->tmdb->profileUrl($data['profile_path'], 'large');
        }

        // Cập nhật ngày sinh / mất nếu chưa có
        if (!$person->date_of_birth && !empty($data['birthday'])) {
            $attributes['date_of_birth'] = $data['birthday'];
        }
        if (!$person->date_of_death && !empty($data['deathday'])) {
            $attributes['date_of_death'] = $data['deathday'];
        }

        // known_for — cập nhật nếu chưa có
        if (!$person->known_for && !empty($data['known_for_department'])) {
            $attributes['known_for'] = $data['known_for_department'];
        }

        $person->update($attributes);
        $this->updated++;
    }
}
