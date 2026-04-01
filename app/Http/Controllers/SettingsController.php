<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    private const TWO_FACTOR_TRUST_COOKIE = 'reco_2fa_trust';

    /**
     * Display the settings dashboard.
     */
    public function index(Request $request)
    {
        // Re-use the existing profile.edit view from Breeze
        // but it could be expanded later to include app-specific preferences (theme, privacy)
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function updateSecurity(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'two_factor_enabled' => ['required', 'boolean'],
            'two_factor_remember_enabled' => ['required', 'boolean'],
        ]);

        $user = $request->user();

        $twoFactorEnabled = (bool) $data['two_factor_enabled'];
        $rememberEnabled = (bool) $data['two_factor_remember_enabled'];

        $user->two_factor_enabled = $twoFactorEnabled;
        $user->two_factor_remember_enabled = $rememberEnabled;

        if (!$twoFactorEnabled || !$rememberEnabled) {
            $user->two_factor_trusted_token_hash = null;
            $user->two_factor_trusted_until = null;
        }

        $user->save();

        $redirect = redirect()->route('settings.index')->with('success', 'Đã cập nhật cài đặt bảo mật.');

        if (!$twoFactorEnabled || !$rememberEnabled) {
            $redirect->withCookie(cookie()->forget(self::TWO_FACTOR_TRUST_COOKIE));
        }

        return $redirect;
    }

    public function trustTwoFactorDevice(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->two_factor_enabled || !$user->two_factor_remember_enabled) {
            return redirect()->back();
        }

        $token = bin2hex(random_bytes(32));

        $user->two_factor_trusted_token_hash = hash('sha256', $token);
        $user->two_factor_trusted_until = now()->addDays(30);
        $user->save();

        return redirect()
            ->back()
            ->with('success', 'Đã lưu đăng nhập trên thiết bị này trong 30 ngày.')
            ->withCookie(cookie(self::TWO_FACTOR_TRUST_COOKIE, $token, 60 * 24 * 30));
    }

    public function dismissTwoFactorTrustPrompt(): RedirectResponse
    {
        return redirect()->back();
    }
}
