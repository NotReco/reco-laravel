@props([])

<nav class="fixed top-0 left-0 right-0 z-50 glass">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 group shrink-0">
                <div
                    class="w-9 h-9 bg-accent-500 rounded-lg flex items-center justify-center group-hover:bg-accent-600 transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                </div>
                <span class="text-xl font-display font-bold text-white">Reco</span>
            </a>

            {{-- Search Bar --}}
            <form action="{{ route('movies.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-8">
                <div class="relative w-full">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="w-full pl-10 pr-4 py-2 bg-dark-800/80 border border-dark-600/50 rounded-xl text-sm text-dark-100 placeholder-dark-400 focus:border-accent-500 focus:ring-1 focus:ring-accent-500 transition-colors"
                        placeholder="Tìm kiếm phim...">
                </div>
            </form>

            {{-- Nav Links + Auth --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('movies.index') }}"
                    class="hidden sm:block text-dark-300 hover:text-white transition-colors text-sm font-medium">Phim</a>

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center gap-2 text-dark-200 hover:text-white transition-colors">
                            <div class="w-8 h-8 rounded-full bg-dark-600 flex items-center justify-center overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <span class="hidden sm:block text-sm font-medium">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 card p-2 shadow-xl">
                            <a href="{{ route('dashboard') }}"
                                class="block px-3 py-2 text-sm text-dark-200 hover:text-white hover:bg-dark-700/50 rounded-lg transition-colors">Dashboard</a>
                            <a href="{{ route('profile.edit') }}"
                                class="block px-3 py-2 text-sm text-dark-200 hover:text-white hover:bg-dark-700/50 rounded-lg transition-colors">Hồ
                                sơ</a>
                            @can('access-admin')
                                <a href="#"
                                    class="block px-3 py-2 text-sm text-accent-400 hover:text-accent-300 hover:bg-dark-700/50 rounded-lg transition-colors">Admin
                                    Panel</a>
                            @endcan
                            <hr class="my-1 border-dark-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 text-sm text-dark-400 hover:text-red-400 hover:bg-dark-700/50 rounded-lg transition-colors">Đăng
                                    xuất</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="text-dark-200 hover:text-white transition-colors text-sm font-medium">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm !py-2 !px-4">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>
</nav>