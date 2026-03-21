<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email là bắt buộc để khôi phục mật khẩu.',
            'email.email' => 'Địa chỉ email có vẻ không hợp lệ.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return redirect()->route('login')->with('status', 'Hướng dẫn đặt lại mật khẩu đã được gửi. Vui lòng kiểm tra hộp thư đến (hoặc hộp thư rác).');
        }

        $errorMessage = $status == Password::INVALID_USER 
            ? 'Không tìm thấy tài khoản nào được liên kết với email này.' 
            : 'Đã có lỗi xảy ra, vui lòng thử lại sau.';

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => $errorMessage]);
    }
}
