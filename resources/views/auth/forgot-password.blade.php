<x-guest-layout>
    <x-slot:title>Quên mật khẩu</x-slot:title>

    <div class="space-y-8">
        {{-- Header --}}
        <div class="text-center">
            <h2 class="text-[22px] font-bold text-gray-900">Khôi phục mật khẩu</h2>
            <p class="mt-2 text-[15px] text-gray-500">Nhập email bạn đã đăng ký và chúng tôi sẽ gửi liên kết đặt lại mật
                khẩu</p>
        </div>

        {{-- Error Block --}}
        @if ($errors->any())
            <div class="bg-white border border-gray-200 rounded-md shadow-sm overflow-hidden mb-6">
                <div class="bg-[#d40242] px-4 py-3 text-white font-bold flex items-center shadow-inner">
                    <svg class="w-5 h-5 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Kiểm tra lại thông tin</span>
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

        {{-- Session Status (Success Message) --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            {{-- Email Address --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Địa chỉ Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-[15px] focus:ring-2 focus:ring-[#01b4e4]/20 focus:border-[#01b4e4] transition-all outline-none"
                    placeholder="Nhập địa chỉ Email của bạn" autofocus>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 px-4 bg-[#01b4e4] hover:bg-[#0090b8] text-white text-[15px] font-bold rounded-xl transition-colors flex items-center justify-center mt-4 shadow-md focus:ring-4 focus:ring-[#01b4e4]/30 outline-none">
                Khôi phục mật khẩu
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

        {{-- Links --}}
        <div class="flex items-center text-[15px] font-medium">
            <div class="flex-1 flex justify-end">
                <a href="{{ route('login') }}"
                    class="text-[#01b4e4] hover:text-[#032541] font-bold transition-colors">
                    Đăng nhập
                </a>
            </div>
            <div
                class="px-3 text-[13px] uppercase font-bold tracking-wide opacity-0 pointer-events-none select-none">
                Hoặc</div>
            <div class="flex-1 flex justify-start">
                <a href="{{ route('register') }}"
                    class="text-[#01b4e4] hover:text-[#032541] font-bold transition-colors">
                    Đăng ký
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
