<?php

namespace App\Http\Controllers;

use App\Models\TvShow;
use App\Models\TvShowVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TvShowVibeController extends Controller
{
    public function update(Request $request, TvShow $tvShow)
    {
        $vibe = TvShowVibe::firstOrNew([
            'tv_show_id' => $tvShow->id,
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

        // Calculate the top 3 moods for this Tv Show
        $topMoods = TvShowVibe::where('tv_show_id', $tvShow->id)
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
