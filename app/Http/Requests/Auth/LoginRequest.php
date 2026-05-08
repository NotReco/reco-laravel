<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Notifications\SuspiciousLoginNotification;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    // Ngưỡng gửi email cảnh báo (lần sai đầu tiên trigger)
    private const WARN_AT  = 3;
    // Ngưỡng khóa tài khoản
    private const LOCK_AT  = 5;
    // Thời gian khóa (phút)
    private const LOCK_MIN = 30;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginValue = $this->input('name');
        $loginType  = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // ── Tìm user trước khi attempt để kiểm tra locked_until ──
        $user = User::where($loginType, '=', $loginValue)->first();

        // Kiểm tra khóa tạm thời (per-account)
        if ($user && $user->isLoginLocked()) {
            $seconds = $user->loginLockedSecondsRemaining();
            $minutes = (int) ceil($seconds / 60);

            throw ValidationException::withMessages([
                'name' => "Tài khoản tạm thời bị khóa do nhập sai mật khẩu quá nhiều lần. "
                    . "Vui lòng thử lại sau {$minutes} phút, hoặc kiểm tra email để đặt lại mật khẩu.",
            ]);
        }

        $credentials = [
            $loginType => $loginValue,
            'password' => $this->input('password'),
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // Ghi nhận thất bại & gửi email nếu cần
            if ($user) {
                $failedCount = $user->incrementFailedLogin();
                $this->handleFailedAttempt($user, $failedCount);
            }

            throw ValidationException::withMessages([
                'name' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Xử lý sau mỗi lần đăng nhập thất bại:
     * - Lần WARN_AT: gửi email cảnh báo
     * - Lần LOCK_AT: gửi email thông báo bị khóa
     */
    private function handleFailedAttempt(User $user, int $failedCount): void
    {
        $ip = $this->ip() ?? 'unknown';

        if ($failedCount === self::LOCK_AT) {
            // Gửi email KHÓA — bypass User::notify() override để đảm bảo luôn gửi
            Notification::route('mail', [$user->email => $user->name])
                ->notify(new SuspiciousLoginNotification('locked', $ip, $failedCount, self::LOCK_MIN));

        } elseif ($failedCount === self::WARN_AT) {
            // Gửi email CẢNH BÁO
            Notification::route('mail', [$user->email => $user->name])
                ->notify(new SuspiciousLoginNotification('warning', $ip, $failedCount, self::LOCK_MIN));
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'name' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('name')).'|'.$this->ip());
    }
}
