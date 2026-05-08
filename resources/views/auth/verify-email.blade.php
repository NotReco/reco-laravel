<x-guest-layout>
    <x-slot:title>Xác minh Email</x-slot:title>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="text-center">
            <div class="w-14 h-14 bg-[#01b4e4]/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-[#01b4e4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-[22px] font-bold text-gray-900">Xác minh email của bạn</h2>
            <p class="mt-2 text-[15px] text-gray-500 leading-relaxed">
                Chúng tôi đã gửi một liên kết xác minh đến địa chỉ email mới của bạn.<br>
                Vui lòng kiểm tra hộp thư và nhấp vào liên kết để hoàn tất.
            </p>
        </div>

        {{-- Flash: đến từ profile update --}}
        @if (session('success'))
            <div class="flex items-start gap-3 p-4 rounded-xl bg-green-50 border border-green-200">
                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Security notice: email cảnh báo đã gửi đến email cũ --}}
        <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm text-amber-700 leading-relaxed">
                Một <strong>email cảnh báo bảo mật</strong> cũng đã được gửi đến địa chỉ email cũ của bạn.
                Nếu bạn không thực hiện thay đổi này, hãy
                <a href="{{ route('password.request') }}" class="font-bold underline hover:text-amber-900">đặt lại mật khẩu ngay</a>.
            </p>
        </div>

        {{-- Info Box --}}
        <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100">
            <svg class="w-5 h-5 text-[#01b4e4] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-gray-600">
                Không nhận được email? Kiểm tra thư mục <strong class="text-gray-800">Spam/Junk</strong> hoặc nhấn nút bên dưới để gửi lại.
            </p>
        </div>

        {{-- Resend Button --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full py-3 px-4 bg-[#01b4e4] hover:bg-[#0090b8] text-white text-[15px] font-bold rounded-xl transition-colors flex items-center justify-center shadow-md focus:ring-4 focus:ring-[#01b4e4]/30 outline-none">
                Gửi lại email xác minh
            </button>
        </form>

        {{-- Actions row --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('profile.edit') }}"
                class="text-[14px] text-gray-500 hover:text-gray-800 font-medium transition-colors">
                ← Quay lại hồ sơ
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-[14px] text-gray-500 hover:text-gray-800 font-medium transition-colors">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
