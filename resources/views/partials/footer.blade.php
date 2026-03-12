{{-- ╔══════════════════════════════════════════════════════════════════╗
║ FOOTER PARTIAL — resources/views/partials/footer.blade.php ║
╚══════════════════════════════════════════════════════════════════╝ --}}

<footer class="mt-20 border-t border-slate-800 bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">

        {{-- Main grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4 group w-fit">
                    <div class="w-8 h-8 bg-rose-600 rounded-lg flex items-center justify-center
                                group-hover:bg-rose-500 transition-colors shadow-md shadow-rose-900/40">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4
                                     M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">Reco</span>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed mb-5">
                    Cổng thông tin đánh giá phim điện ảnh tích hợp trợ lý AI gợi ý thông minh.
                </p>
                {{-- Social --}}
                <div class="flex gap-2">
                    <a href="#" title="Facebook" class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center
                              text-slate-400 hover:text-white hover:bg-slate-700 transition-all duration-150">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="#" title="GitHub" class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center
                              text-slate-400 hover:text-white hover:bg-slate-700 transition-all duration-150">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                    <a href="#" title="YouTube" class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center
                              text-slate-400 hover:text-white hover:bg-slate-700 transition-all duration-150">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Khám phá --}}
            <div>
                <h4 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-3.5 bg-rose-500 rounded-full"></span>
                    Khám phá
                </h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('explore') }}"
                            class="text-slate-400 hover:text-white transition-colors">Tất cả phim</a></li>
                    <li><a href="{{ route('explore', ['sort' => 'top_rated']) }}"
                            class="text-slate-400 hover:text-white transition-colors">Đánh giá cao nhất</a></li>
                    <li><a href="{{ route('explore', ['sort' => 'popular']) }}"
                            class="text-slate-400 hover:text-white transition-colors">Phổ biến nhất</a></li>
                    <li><a href="{{ route('explore', ['sort' => 'latest']) }}"
                            class="text-slate-400 hover:text-white transition-colors">Mới nhất</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Diễn đàn</a></li>
                </ul>
            </div>

            {{-- Thông tin --}}
            <div>
                <h4 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-3.5 bg-rose-500 rounded-full"></span>
                    Thông tin
                </h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Giới thiệu</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Bảo mật & Quyền riêng
                            tư</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Điều khoản sử dụng</a>
                    </li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Liên hệ</a></li>
                </ul>
            </div>

            {{-- Stack --}}
            <div>
                <h4 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-3.5 bg-rose-500 rounded-full"></span>
                    Công nghệ
                </h4>
                <div class="space-y-2.5 text-sm text-slate-400">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded bg-red-500/20 border border-red-500/30 flex items-center justify-center text-[10px] font-bold text-red-400">L</span>
                        Laravel 12
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded bg-sky-500/20 border border-sky-500/30 flex items-center justify-center text-[10px] font-bold text-sky-400">T</span>
                        Tailwind CSS
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded bg-green-500/20 border border-green-500/30 flex items-center justify-center text-[10px] font-bold text-green-400">AI</span>
                        Gợi ý thông minh
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded bg-yellow-500/20 border border-yellow-500/30 flex items-center justify-center text-[10px] font-bold text-yellow-400">DB</span>
                        TMDb API
                    </div>
                </div>
            </div>
        </div>

        {{-- Guest CTA --}}
        @guest
            <div class="card bg-gradient-to-br from-rose-900/40 to-slate-900/40 border-rose-500/20 p-8 text-center rounded-2xl mb-12">
                <h3 class="text-2xl font-display font-bold text-white mb-2">Tham gia cộng đồng RecoDB</h3>
                <p class="text-slate-400 max-w-xl mx-auto mb-6">Đăng ký tài khoản để đánh giá phim, tạo danh sách yêu thích và tham gia thảo luận cùng hàng ngàn thành viên khác.</p>
                <div class="flex items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="btn-primary">Tạo tài khoản miễn phí</a>
                    <a href="{{ route('login') }}" class="btn-secondary">Đăng nhập</a>
                </div>
            </div>
        @endguest

        {{-- Bottom bar --}}
        <div
            class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-slate-500">
            <p>© {{ date('Y') }} <span class="text-slate-400 font-medium">Reco</span> — Chuyên đề tốt nghiệp</p>
            <p>Nguyễn Đức Thông · MSSV: 64132336</p>
        </div>
    </div>
</footer>