<x-guest-layout>
    <x-slot:title>Đăng ký</x-slot:title>

    <div class="space-y-8">
        {{-- Header --}}
        <div class="text-center">
            <h2 class="text-[22px] font-bold text-gray-900">Tạo tài khoản</h2>
            <p class="mt-2 text-[15px] text-gray-500">Gia nhập cộng đồng đánh giá phim RecoDB</p>
        </div>

        {{-- TMDB Error Block --}}
        @if ($errors->any())
            <div class="bg-white border border-gray-200 rounded-md shadow-sm overflow-hidden mb-6">
                <div class="bg-[#d40242] px-4 py-3 text-white font-bold flex items-center shadow-inner">
                    <svg class="w-5 h-5 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Đã có lỗi xảy ra trong quá trình đăng ký</span>
                </div>
                <div class="p-4 bg-white">
                    <ul class="list-disc pl-5 text-gray-800 text-[14px] space-y-1">
                        @foreach ($errors->unique() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Tên đăng nhập</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-[15px] focus:ring-2 focus:ring-[#01b4e4]/20 focus:border-[#01b4e4] transition-all outline-none"
                    placeholder="Tên đăng nhập" autofocus autocomplete="name">
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-[15px] focus:ring-2 focus:ring-[#01b4e4]/20 focus:border-[#01b4e4] transition-all outline-none"
                    placeholder="Email" autocomplete="username">
            </div>

            {{-- Password --}}
            <div x-data="{ show: false }">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Mật khẩu</label>
                <div class="relative">
                    <input id="password" :type="show ? 'text' : 'password'" name="password"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-[15px] focus:ring-2 focus:ring-[#01b4e4]/20 focus:border-[#01b4e4] transition-all outline-none pr-12"
                        placeholder="Mật khẩu" autocomplete="new-password">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="show" style="display: none" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Xác nhận mật
                    khẩu</label>
                <div class="relative">
                    <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-[15px] focus:ring-2 focus:ring-[#01b4e4]/20 focus:border-[#01b4e4] transition-all outline-none pr-12"
                        placeholder="Xác nhận mật khẩu" autocomplete="new-password">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="show" style="display: none" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                </div>
            </div>

            {{-- Terms & Privacy Disclaimer --}}
            <p class="text-[13px] text-gray-500 leading-relaxed mt-2">
                Bằng việc bấm nút Đăng ký bên dưới, bạn xác nhận đã đọc và đồng ý với
                <a href="{{ route('pages.terms') }}" tabindex="-1"
                    class="text-[#01b4e4] hover:text-[#032541] font-semibold transition-colors">Điều khoản dịch vụ</a>
                và
                <a href="{{ route('pages.privacy') }}" tabindex="-1"
                    class="text-[#01b4e4] hover:text-[#032541] font-semibold transition-colors">Chính sách bảo mật</a>
                của RecoDB.
            </p>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 px-4 bg-[#01b4e4] hover:bg-[#0090b8] text-white text-[15px] font-bold rounded-xl transition-colors flex items-center justify-center mt-3 shadow-md focus:ring-4 focus:ring-[#01b4e4]/30 outline-none">
                Đăng ký
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

        {{-- Login link --}}
        <p class="text-center text-[15px] text-gray-600 font-medium">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="text-[#01b4e4] hover:text-[#032541] font-bold transition-colors">
                Đăng nhập ngay
            </a>
        </p>
    </div>
</x-guest-layout>
