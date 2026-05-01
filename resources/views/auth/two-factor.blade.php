<x-guest-layout>
    <x-slot:title>Xác thực hai lớp</x-slot:title>

    <div class="space-y-8">
        {{-- Header --}}
        <div class="text-center">
            <div class="w-14 h-14 bg-[#01b4e4]/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-[#01b4e4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-[22px] font-bold text-gray-900">Xác thực hai lớp</h2>
            <p class="mt-2 text-[15px] text-gray-500">Mã xác thực 6 số đã được gửi đến email của bạn</p>
        </div>


        <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-5">
            @csrf

            {{-- Code Input --}}
            <div>
                <label for="code" class="block text-sm font-semibold text-gray-700 mb-1.5">Mã xác thực</label>
                <input id="code" type="text" name="code"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-2xl text-center tracking-[0.5em] font-mono focus:ring-2 focus:ring-[#01b4e4]/20 focus:border-[#01b4e4] transition-all outline-none"
                    placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required autofocus
                    autocomplete="one-time-code">
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            {{-- Info Box --}}
            <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100">
                <svg class="w-5 h-5 text-[#01b4e4] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-gray-600">
                    Kiểm tra hộp thư email của bạn. Mã có hiệu lực trong <strong class="text-gray-800">10 phút</strong>.
                </p>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 px-4 bg-[#01b4e4] hover:bg-[#0090b8] text-white text-[15px] font-bold rounded-xl transition-colors flex items-center justify-center shadow-md focus:ring-4 focus:ring-[#01b4e4]/30 outline-none">
                Xác thực
            </button>
        </form>

        {{-- Resend & Back --}}
        <div class="flex items-center justify-between text-[14px] font-medium">
            <form method="POST" action="{{ route('2fa.resend') }}">
                @csrf
                <button type="submit" class="text-[#01b4e4] hover:text-[#032541] transition-colors">
                    Gửi lại mã
                </button>
            </form>

            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-800 transition-colors">
                ← Quay lại đăng nhập
            </a>
        </div>
    </div>
</x-guest-layout>