<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the specified user's public profile.
     */
    public function show(User $user)
    {
        $user->loadCount(['followers', 'following', 'reviews'])
            ->load([
                'favorites' => fn($q) => $q->latest()->take(6),
                'reviews' => fn($q) => $q->with('movie')->latest()->take(5),
                'activeTitle',
                'activeFrame',
                'topMovies' => fn($q) => $q->orderBy('user_top_movies.order'),
            ]);

        $isOwnProfile = Auth::check() && Auth::id() === $user->id;
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $isFollowing = Auth::check() && !$isOwnProfile 
            ? $authUser->following()->where('following_id', $user->id)->exists() 
            : false;

        $stats = [
            'reviews_count' => $user->reviews_count,
            'followers_count' => $user->followers_count,
            'following_count' => $user->following_count,
            'favorites_count' => $user->favorites()->count(),
            'watch_time' => 0 // Gợi ý: có thể tính tổng thời lượng phim đã xem
        ];

        return view('profile.show', compact('user', 'isOwnProfile', 'isFollowing', 'stats'));
    }

    /**
     * Display the specified user's favorite movies.
     */
    public function favorites(User $user)
    {
        $favorites = $user->favorites()->latest()->paginate(24);
        
        $isOwnProfile = Auth::check() && Auth::id() === $user->id;
        
        return view('profile.favorites', compact('user', 'favorites', 'isOwnProfile'));
    }



    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load(['titles', 'frames', 'topMovies' => fn($q) => $q->orderBy('user_top_movies.order')]);

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar && !str_starts_with($request->user()->avatar, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $request->user()->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/' . $path;
        } elseif ($request->boolean('remove_avatar')) {
            if ($request->user()->avatar && !str_starts_with($request->user()->avatar, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $request->user()->avatar));
            }
            $data['avatar'] = null;
        }

        if ($request->hasFile('cover_photo')) {
            if ($request->user()->cover_photo && !str_starts_with($request->user()->cover_photo, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $request->user()->cover_photo));
            }
            $path = $request->file('cover_photo')->store('covers', 'public');
            $data['cover_photo'] = '/storage/' . $path;
        } elseif ($request->boolean('remove_cover')) {
            if ($request->user()->cover_photo && !str_starts_with($request->user()->cover_photo, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $request->user()->cover_photo));
            }
            $data['cover_photo'] = null;
        }

        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Sync Top 4 Movies
        if ($request->has('top_movies')) {
            $syncData = [];
            foreach ($request->top_movies as $index => $movieId) {
                if ($movieId) {
                    $syncData[$movieId] = ['order' => $index + 1];
                }
            }
            $request->user()->topMovies()->sync($syncData);
        } else {
            // Nếu không gửi top_movies, có thể người dùng đã clear (trống array)
            // Hoặc form không chứa, cần check
            if ($request->exists('top_movies_submitted')) {
                $request->user()->topMovies()->sync([]);
            }
        }

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
