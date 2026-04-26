<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieVibeController extends Controller
{
    public function update(Request $request, Movie $movie)
    {
        $vibe = MovieVibe::firstOrNew([
            'movie_id' => $movie->id,
            'user_id' => auth()->id()
        ]);

        if ($request->has('mood')) {
            $mood = $request->input('mood');
            // Toggle off if clicking the same mood
            $vibe->mood = ($vibe->mood === $mood) ? null : $mood;
        }

        if ($request->has('tone')) {
            $tone = $request->input('tone');
            $vibe->tone = $tone === '' ? null : $tone;
        }

        $vibe->save();

        // Calculate the top 3 moods for this movie
        $topMoods = MovieVibe::where('movie_id', $movie->id)
            ->whereNotNull('mood')
            ->groupBy('mood')
            ->select('mood', DB::raw('count(*) as count'))
            ->orderByDesc('count')
            ->limit(3)
            ->pluck('mood');

        return response()->json([
            'success' => true,
            'mood' => $vibe->mood,
            'tone' => $vibe->tone,
            'top_moods' => $topMoods
        ]);
    }
}
