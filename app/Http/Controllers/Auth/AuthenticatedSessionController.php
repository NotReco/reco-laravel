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
                'email' => 'Tài khoản này hiện đang bị khóa. Liên hệ Admin để được kích hoạt.',
            ])->onlyInput('email');
        }

        // ── Admin cần 2FA ──
        if ($user->requiresTwoFactor()) {
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
