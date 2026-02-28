<x-guest-layout>
    @section('title', 'Đăng nhập')

    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h2 class="text-2xl font-display font-bold text-white">Chào mừng trở lại</h2>
            <p class="mt-2 text-dark-400">Đăng nhập để tiếp tục khám phá thế giới điện ảnh</p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-dark-200 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="input-dark"
                    placeholder="you@example.com" required autofocus autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-dark-200 mb-2">Mật khẩu</label>
                <input id="password" type="password" name="password" class="input-dark" placeholder="••••••••" required
                    autocomplete="current-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember & Forgot --}}
            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="w-4 h-4 rounded bg-dark-700 border-dark-500 text-accent-500 focus:ring-accent-500 focus:ring-offset-dark-900 cursor-pointer">
                    <span class="text-sm text-dark-300">Ghi nhớ đăng nhập</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-accent-400 hover:text-accent-300 transition-colors">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Đăng nhập
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-dark-700"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-dark-950 text-dark-400">hoặc</span>
            </div>
        </div>

        {{-- Register link --}}
        <p class="text-center text-dark-300">
            Chưa có tài khoản?
            <a href="{{ route('register') }}"
                class="text-accent-400 hover:text-accent-300 font-semibold transition-colors">
                Đăng ký ngay
            </a>
        </p>
    </div>
</x-guest-layout>