<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    /**
     * Hiển thị form nhập mã 2FA.
     */
    public function show(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    /**
     * Xác thực mã 2FA.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('2fa:user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['code' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.']);
        }

        $user = User::find($userId);

        if (!$user) {
            $request->session()->forget('2fa:user_id');
            return redirect()->route('login');
        }

        // Kiểm tra mã hết hạn
        if ($user->two_factor_expires_at && $user->two_factor_expires_at->isPast()) {
            $user->clearTwoFactorCode();
            $request->session()->forget('2fa:user_id');

            return redirect()->route('login')
                ->withErrors(['code' => 'Mã xác thực đã hết hạn. Vui lòng đăng nhập lại.']);
        }

        // Kiểm tra mã đúng
        if ($request->code !== $user->two_factor_code) {
            return back()->withErrors(['code' => 'Mã xác thực không đúng. Vui lòng thử lại.']);
        }

        // ✅ Xác thực thành công
        $user->clearTwoFactorCode();
        $user->update(['last_login_at' => now()]);

        $request->session()->forget('2fa:user_id');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Gửi lại mã 2FA.
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('2fa:user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if ($user) {
            $user->generateTwoFactorCode();
            $user->notify(new TwoFactorCode());
        }

        return back()->with('status', 'Mã xác thực mới đã được gửi đến email của bạn.');
    }
}
