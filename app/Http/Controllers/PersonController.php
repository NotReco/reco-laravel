<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Hiển thị chi tiết diễn viên/đạo diễn/biên kịch.
     */
    public function show(Person $person)
    {
        // Load các bộ phim mà người này tham gia đóng (cast)
        $actedMovies = $person->moviesAsCast()
            ->with('genres') // eager load để dùng trong x-movie-card
            ->orderByDesc('release_date')
            ->get();

        // Load các bộ phim mà người này làm crew (đạo diễn, biên kịch,...)
        $crewedMovies = $person->moviesAsCrew()
            ->with(['genres'])
            ->orderByDesc('release_date')
            ->get();

        $crewedByJob = $crewedMovies->groupBy('pivot.job');

        return view('person.show', compact('person', 'actedMovies', 'crewedByJob'));
    }
}
