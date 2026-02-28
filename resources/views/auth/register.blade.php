<x-guest-layout>
    @section('title', 'Đăng ký')

    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h2 class="text-2xl font-display font-bold text-white">Tạo tài khoản</h2>
            <p class="mt-2 text-dark-400">Tham gia cộng đồng yêu phim và nhận gợi ý từ AI</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-dark-200 mb-2">Họ và tên</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" class="input-dark"
                    placeholder="Nguyễn Văn A" required autofocus autocomplete="name">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-dark-200 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="input-dark"
                    placeholder="you@example.com" required autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-dark-200 mb-2">Mật khẩu</label>
                <input id="password" type="password" name="password" class="input-dark" placeholder="Tối thiểu 8 ký tự"
                    required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-dark-200 mb-2">Xác nhận mật
                    khẩu</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="input-dark"
                    placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Đăng ký tài khoản
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

        {{-- Login link --}}
        <p class="text-center text-dark-300">
            Đã có tài khoản?
            <a href="{{ route('login') }}"
                class="text-accent-400 hover:text-accent-300 font-semibold transition-colors">
                Đăng nhập
            </a>
        </p>
    </div>
</x-guest-layout>