<footer class="bg-[#032541]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Main Group: Centered layout similar to TMDB --}}
        <div class="flex flex-col md:flex-row items-center md:items-start justify-center gap-12 md:gap-16 mb-8">

            {{-- Left Side: Right-aligned (applies to md+ screens) --}}
            <div class="flex flex-col items-center md:items-end">
                <a href="{{ route('home') }}" class="inline-block mb-6">
                    <img src="{{ asset('storage/images/logo-dark.png') }}" alt="RecoDB" class="h-10 w-auto">
                </a>
                @auth
                    <a href="{{ route('forum.index') }}"
                        class="inline-block px-6 py-2 bg-white rounded-md text-sm font-bold tracking-wider text-[#01b4e4] hover:underline underline-offset-2 transition-all uppercase">
                        Khám phá diễn đàn
                    </a>
                @else
                    <a href="{{ route('register') }}"
                        class="inline-block px-6 py-2 bg-white rounded-md text-sm font-bold tracking-wider text-[#01b4e4] hover:underline underline-offset-2 transition-all uppercase">
                        Tham gia cộng đồng
                    </a>
                @endauth
            </div>

            {{-- Community --}}
            <div class="flex flex-col items-center md:items-start">
                <h4 class="text-base font-bold text-white uppercase tracking-wider mb-4">Cộng đồng</h4>
                <ul class="space-y-2.5 text-sm text-center md:text-left">
                    <li>
                        <a href="https://zalo.me/g/5o5ee8fot0igcgsrooht" target="_blank" rel="noopener noreferrer" class="text-blue-100 hover:text-white hover:underline transition-colors flex items-center gap-2 justify-center md:justify-start">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22.048 11.233c0-4.99-4.886-9.034-10.912-9.034C5.112 2.2 0 6.244 0 11.233c0 2.65 1.4 5.05 3.633 6.643V22.25c0 .542.617.857 1.074.562l3.666-2.355c.91.26 1.875.405 2.875.405 6.026 0 10.912-4.044 10.912-9.034h-.112zm-6.935 3.42H10.15c-.32 0-.578-.26-.578-.577s.26-.577.578-.577h2.443l-2.32-2.833a.604.604 0 0 1-.068-.168v-1.253c0-.32.26-.578.578-.578h4.963c.32 0 .578.26.578.578s-.26.578-.578.578h-2.443l2.32 2.833c.044.054.067.112.067.18v1.25c.002.28-.27.545-.578.545z"/></svg>
                            Nhóm Zalo
                        </a>
                    </li>
                    <li>
                        <a href="https://www.facebook.com/groups/recodb" target="_blank" rel="noopener noreferrer" class="text-blue-100 hover:text-white hover:underline transition-colors flex items-center gap-2 justify-center md:justify-start">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                            Nhóm Facebook
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Right Side: Left-aligned (applies to md+ screens) --}}
            <div class="flex flex-col items-center md:items-start">
                <h4 class="text-base font-bold text-white uppercase tracking-wider mb-4">Pháp lý</h4>
                <ul class="space-y-2.5 text-sm text-center md:text-left">
                    <li><a href="{{ route('pages.terms') }}"
                            class="text-blue-100 hover:text-white hover:underline transition-colors">Điều khoản dịch
                            vụ</a></li>
                    <li><a href="{{ route('pages.privacy') }}"
                            class="text-blue-100 hover:text-white hover:underline transition-colors">Chính sách bảo
                            mật</a></li>
                </ul>
            </div>

        </div>

        {{-- Copyright --}}
        <div>
            <p class="text-center text-xs text-blue-200/50">
                © {{ date('Y') }} - RecoDB
            </p>
        </div>
    </div>
</footer>
