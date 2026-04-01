<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    private const TWO_FACTOR_TRUST_COOKIE = 'reco_2fa_trust';

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // ── Kiểm tra tài khoản bị khóa (mod lock) ──
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'name' => 'Tài khoản này hiện đang bị khóa. Liên hệ Admin để được kích hoạt.',
            ])->onlyInput('name');
        }

        // ── 2FA ──
        if ($user->requiresTwoFactor() && !$this->hasTrustedDevice($request, $user)) {
            // Tạo mã 2FA và gửi email
            $user->generateTwoFactorCode();
            $user->notify(new TwoFactorCode());

            // Lưu user_id vào session, chưa đăng nhập chính thức
            $userId = $user->id;
            Auth::logout();

            $request->session()->put('2fa:user_id', $userId);
            $request->session()->regenerate();

            return redirect()->route('2fa.verify');
        }

        // ── Đăng nhập thường ──
        $user->update(['last_login_at' => now()]);
        $request->session()->regenerate();

        return redirect()->intended(route('home', absolute: false));
    }

    private function hasTrustedDevice(Request $request, $user): bool
    {
        if (!$user->two_factor_remember_enabled) {
            return false;
        }

        if (!$user->two_factor_trusted_token_hash || !$user->two_factor_trusted_until) {
            return false;
        }

        if ($user->two_factor_trusted_until->isPast()) {
            return false;
        }

        $rawToken = (string) $request->cookie(self::TWO_FACTOR_TRUST_COOKIE, '');
        if (!$rawToken) {
            return false;
        }

        return hash_equals($user->two_factor_trusted_token_hash, hash('sha256', $rawToken));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
