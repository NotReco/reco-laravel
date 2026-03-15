<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the specified user's public profile.
     */
    public function show($id)
    {
        $user = User::withCount(['followers', 'following', 'reviews'])
            ->with([
                'favoriteMovies' => fn($q) => $q->latest()->take(6),
                'reviews' => fn($q) => $q->with('movie')->latest()->take(5)
            ])
            ->findOrFail($id);

        $isOwnProfile = Auth::check() && Auth::id() === $user->id;
        $isFollowing = Auth::check() && !$isOwnProfile 
            ? Auth::user()->following()->where('following_id', $user->id)->exists() 
            : false;

        $stats = [
            'reviews_count' => $user->reviews_count,
            'followers_count' => $user->followers_count,
            'following_count' => $user->following_count,
            'favorites_count' => $user->favoriteMovies()->count(),
            'watch_time' => 0 // Gợi ý: có thể tính tổng thời lượng phim đã xem
        ];

        return view('profile.show', compact('user', 'isOwnProfile', 'isFollowing', 'stats'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Hardcode handle avatar upload if we add it later
        if ($request->hasFile('avatar')) {
             // Handle file upload here
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'Hồ sơ đã được cập nhật.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Tài khoản của bạn đã được xóa.');
    }
}
