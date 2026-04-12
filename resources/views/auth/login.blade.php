<x-guest-layout>
    <x-slot:title>Đăng nhập</x-slot:title>

    <div class="space-y-8">
        {{-- Header --}}
        <div class="text-center">
            <h2 class="text-[22px] font-bold text-gray-900">Chào mừng trở lại</h2>
            <p class="mt-2 text-[15px] text-gray-500">Xem, chấm điểm và chia sẻ gu phim của bạn</p>
        </div>

        {{-- Bubble Notification (Toast) --}}
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                class="fixed top-5 right-5 z-50 bg-[#01b4e4] text-white px-6 py-4 rounded-xl shadow-2xl flex items-start gap-4 mx-4 sm:mx-0 max-w-sm"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-10"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-10"
                style="display: none;">
                <svg class="w-6 h-6 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="font-bold text-[15px]">Kiểm tra hộp thư</h4>
                    <p class="text-[13px] opacity-90 mt-1 leading-relaxed">{{ session('status') }}</p>
                </div>
                <button @click="show = false" class="ml-auto opacity-70 hover:opacity-100 transition-opacity mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5" novalidate>
            @csrf

            {{-- Username or Email --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Tên đăng nhập hoặc
                    Email</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-[15px] focus:ring-2 focus:ring-[#01b4e4]/20 focus:border-[#01b4e4] transition-all outline-none"
                    placeholder="Tên đăng nhập hoặc Email" required autofocus autocomplete="username">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div x-data="{ show: false }">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Mật khẩu</label>
                <div class="relative">
                    <input id="password" :type="show ? 'text' : 'password'" name="password"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-[15px] focus:ring-2 focus:ring-[#01b4e4]/20 focus:border-[#01b4e4] transition-all outline-none pr-12"
                        placeholder="Mật khẩu" required autocomplete="current-password">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="show" style="display: none" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember & Forgot --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="w-4 h-4 rounded border-gray-300 text-[#01b4e4] focus:ring-0 focus:ring-offset-0 cursor-pointer">
                    <span class="text-[14px] text-gray-600 font-medium">Ghi nhớ mật khẩu</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-[14px] font-medium text-[#01b4e4] hover:text-[#032541] transition-colors">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 px-4 bg-[#01b4e4] hover:bg-[#0090b8] text-white text-[15px] font-bold rounded-xl transition-colors flex items-center justify-center mt-2 shadow-md focus:ring-4 focus:ring-[#01b4e4]/30 outline-none">
                Đăng nhập
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-[13px] uppercase font-bold tracking-wide">
                <span class="px-3 bg-white text-gray-400">Hoặc</span>
            </div>
        </div>

        {{-- Register link --}}
        <p class="text-center text-[15px] text-gray-600 font-medium">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="text-[#01b4e4] hover:text-[#032541] font-bold transition-colors">
                Tạo tài khoản ngay
            </a>
        </p>
    </div>
</x-guest-layout>
