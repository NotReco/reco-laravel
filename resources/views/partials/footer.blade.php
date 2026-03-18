<footer class="border-t border-gray-200/80 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Main Group: Centered layout similar to TMDB --}}
        <div class="flex flex-col md:flex-row items-center md:items-start justify-center gap-12 md:gap-16 mb-8">

            {{-- Left Side: Right-aligned (applies to md+ screens) --}}
            <div class="flex flex-col items-center md:items-end">
                <a href="{{ route('home') }}" class="inline-block mb-6">
                    <img src="{{ asset('storage/images/logo.svg') }}" alt="RecoDB" class="h-10 w-auto">
                </a>
                <a href="{{ route('register') }}"
                    class="inline-block px-6 py-2 bg-gray-800 rounded-md text-sm font-bold tracking-wider text-white hover:underline underline-offset-2 transition-all uppercase">
                    Tham gia cộng đồng
                </a>
            </div>

            {{-- Right Side: Left-aligned (applies to md+ screens) --}}
            <div class="flex flex-col items-center md:items-start">
                <h4 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-4">Pháp lý</h4>
                <ul class="space-y-2.5 text-sm text-center md:text-left">
                    <li><a href="{{ route('pages.terms') }}"
                            class="text-gray-600 hover:text-gray-900 hover:underline transition-colors">Điều khoản dịch
                            vụ</a></li>
                    <li><a href="{{ route('pages.privacy') }}"
                            class="text-gray-600 hover:text-gray-900 hover:underline transition-colors">Chính sách bảo
                            mật</a></li>
                </ul>
            </div>

        </div>

        {{-- Copyright --}}
        <div>
            <p class="text-center text-xs text-gray-400">
                © {{ date('Y') }} - RecoDB
            </p>
        </div>
    </div>
</footer>
