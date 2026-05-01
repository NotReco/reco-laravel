<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Danh sách diễn viên / đạo diễn.
     */
    public function index(Request $request)
    {
        $query = Person::query();

        if ($q = trim($request->input('q', ''))) {
            $safe = str_replace(['%', '_'], '', $q);
            $query->where('name', 'like', "%{$safe}%");
        }

        if ($role = $request->input('known_for')) {
            $query->where('known_for', $role);
        }

        $people = $query
            ->withCount('movies')
            ->orderByDesc('movies_count')
            ->paginate(36)
            ->withQueryString();

        // AJAX: trả JSON cho Alpine.js
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'total'    => $people->total(),
                'items'    => $people->map(fn($p) => [
                    'name'         => $p->name,
                    'photo'        => $p->photo,
                    'known_for'    => $p->known_for,
                    'movies_count' => $p->movies_count,
                    'url'          => route('person.show', $p),
                ]),
                'has_more'  => $people->hasMorePages(),
                'next_page' => $people->currentPage() + 1,
            ]);
        }

        $roles = Person::select('known_for')
            ->distinct()
            ->whereNotNull('known_for')
            ->pluck('known_for');

        return view('person.index', compact('people', 'roles'));
    }

    /**
     * Hiển thị chi tiết diễn viên/đạo diễn/biên kịch (TMDB-style).
     */
    public function show(Person $person)
    {
        // ── Diễn xuất ──────────────────────────────────────────────────────
        $actedMovies = $person->moviesAsActor()
            ->with('genres')
            ->orderByDesc('release_date')
            ->get();

        // Nhóm theo năm (key = năm, fallback "Sắp ra mắt")
        $actedByYear = $actedMovies->groupBy(function ($movie) {
            return $movie->release_date?->format('Y') ?? 'TBA';
        })->sortKeysDesc();

        // ── Known For – top 8 nổi bật nhất (theo display_order, sau đó ngày phát hành) ──
        $knownForMovies = $person->moviesAsActor()
            ->orderByPivot('display_order')
            ->orderByDesc('release_date')
            ->limit(8)
            ->get();

        // Nếu là diễn viên có ít phim, thêm phim từ crew
        if ($knownForMovies->count() < 4) {
            $knownForMovies = $person->movies()
                ->orderByDesc('release_date')
                ->limit(8)
                ->get();
        }

        // ── Crew: gom theo công việc ────────────────────────────────────────
        $crewedByJob = collect();

        $directors = $person->moviesAsDirector()->with('genres')->orderByDesc('release_date')->get();
        if ($directors->isNotEmpty()) {
            $crewedByJob['Director'] = $directors->groupBy(
                fn($m) => $m->release_date?->format('Y') ?? 'TBA'
            )->sortKeysDesc();
        }

        $writers = $person->moviesAsWriter()->with('genres')->orderByDesc('release_date')->get();
        if ($writers->isNotEmpty()) {
            $crewedByJob['Writer'] = $writers->groupBy(
                fn($m) => $m->release_date?->format('Y') ?? 'TBA'
            )->sortKeysDesc();
        }

        $producers = $person->moviesAsProducer()->with('genres')->orderByDesc('release_date')->get();
        if ($producers->isNotEmpty()) {
            $crewedByJob['Producer'] = $producers->groupBy(
                fn($m) => $m->release_date?->format('Y') ?? 'TBA'
            )->sortKeysDesc();
        }

        // Tổng số phim
        $totalCredits = $actedMovies->count()
            + $directors->count()
            + $writers->count()
            + $producers->count();

        return view('person.show', compact(
            'person',
            'actedMovies',
            'actedByYear',
            'knownForMovies',
            'crewedByJob',
            'totalCredits'
        ));
    }
}
