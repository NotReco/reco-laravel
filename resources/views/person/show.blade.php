<x-app-layout>
<x-slot:title>{{ $person->name }} – Diễn viên</x-slot:title>

@php
    $bioText = $person->biography ?? $person->bio ?? '';
    $roleMap = [
        'Acting'     => ['label' => 'Diễn xuất',  'color' => 'bg-sky-500/20 text-sky-300 border-sky-500/40'],
        'Directing'  => ['label' => 'Đạo diễn',   'color' => 'bg-amber-500/20 text-amber-300 border-amber-500/40'],
        'Writing'    => ['label' => 'Biên kịch',  'color' => 'bg-purple-500/20 text-purple-300 border-purple-500/40'],
        'Production' => ['label' => 'Sản xuất',   'color' => 'bg-green-500/20 text-green-300 border-green-500/40'],
    ];
    $roleStyle = $roleMap[$person->known_for] ?? ['label' => $person->known_for, 'color' => 'bg-gray-500/20 text-gray-300 border-gray-500/40'];
    $jobLabels = ['Director' => 'Đạo diễn', 'Writer' => 'Biên kịch', 'Producer' => 'Sản xuất'];
@endphp

<style>
/* ── Person Hero ── */
.person-hero {
    background: #0d111a;
    position: relative;
    overflow: hidden;
}
.person-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 20% 50%, rgba(99,102,241,.18) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(236,72,153,.12) 0%, transparent 50%);
    pointer-events: none;
}
.person-poster {
    width: 240px;
    min-width: 240px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.08);
    aspect-ratio: 2/3;
    background: #1e2535;
    flex-shrink: 0;
}
.person-poster img { width:100%; height:100%; object-fit:cover; }

/* ── Sidebar ── */
.person-sidebar {
    width: 280px;
    min-width: 280px;
    flex-shrink: 0;
}
.sidebar-social-btn {
    display: flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 50%;
    border: 1.5px solid #e2e8f0;
    color: #64748b; transition: all .2s;
    text-decoration: none;
}
.sidebar-social-btn:hover { border-color: #6366f1; color: #6366f1; background: #eef2ff; }
.sidebar-info-row { display: flex; flex-direction: column; gap: 2px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
.sidebar-info-row:last-child { border-bottom: none; }
.sidebar-info-label { font-size: 12px; font-weight: 700; color: #94a3b8; letter-spacing: .04em; text-transform: uppercase; }
.sidebar-info-value { font-size: 14px; color: #1e293b; font-weight: 500; line-height: 1.5; }

/* ── Known For Gallery ── */
.known-for-track { display: flex; gap: 16px; overflow-x: auto; scrollbar-width: none; padding-bottom: 4px; }
.known-for-track::-webkit-scrollbar { display: none; }
.known-for-card {
    flex-shrink: 0; width: 120px; text-decoration: none; group;
    border-radius: 10px; overflow: hidden;
}
.known-for-card-img {
    width: 120px; height: 180px; border-radius: 10px; overflow: hidden;
    background: #e2e8f0;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
    transition: transform .25s, box-shadow .25s;
}
.known-for-card:hover .known-for-card-img { transform: scale(1.04); box-shadow: 0 8px 28px rgba(0,0,0,.22); }
.known-for-card-img img { width:100%; height:100%; object-fit:cover; }
.known-for-card-title { font-size: 12px; font-weight: 600; color: #1e293b; margin-top: 8px; text-align: center; line-height: 1.3; }

/* ── Credits Timeline ── */
.credits-section-title {
    font-size: 20px; font-weight: 800; color: #0f172a;
    display: flex; align-items: center; gap: 10px;
}
.credits-year-group { margin-bottom: 0; }
.credits-year-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 52px; padding: 2px 10px;
    font-size: 13px; font-weight: 700; color: #475569;
    background: #f1f5f9; border-radius: 99px;
    flex-shrink: 0;
}
.credits-year-badge.tba { color: #94a3b8; background: #f8fafc; }
.credit-row {
    display: flex; align-items: center; gap: 14px;
    padding: 10px 12px; border-radius: 10px;
    transition: background .18s;
    text-decoration: none; color: inherit;
}
.credit-row:hover { background: #f8fafc; }
.credit-dot { width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; flex-shrink: 0; }
.credit-row:hover .credit-dot { background: #6366f1; }
.credit-poster { width: 44px; height: 62px; border-radius: 6px; overflow: hidden; background: #f1f5f9; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
.credit-poster img { width:100%; height:100%; object-fit:cover; }
.credit-title { font-size: 14px; font-weight: 600; color: #1e293b; }
.credit-row:hover .credit-title { color: #6366f1; }
.credit-char { font-size: 12px; color: #94a3b8; font-style: italic; margin-top: 2px; }

[x-cloak] { display: none !important; }

.mask-bottom {
    -webkit-mask-image: linear-gradient(180deg, #000 60%, transparent 100%);
    mask-image: linear-gradient(180deg, #000 60%, transparent 100%);
}
.mask-none {
    -webkit-mask-image: none !important;
    mask-image: none !important;
}

/* Responsive */
@media(max-width: 1023px) {
    .person-sidebar { width: 100%; min-width: 0; }
    .person-poster { width: 160px; min-width: 160px; }
}
@media(max-width: 639px) {
    .person-poster { width: 120px; min-width: 120px; }
}
</style>

{{-- ══════════════════ HERO ══════════════════ --}}
<section class="person-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
        {{-- Back --}}
        <a href="{{ route('person.index') }}"
           class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-white transition-colors mb-6">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Diễn viên &amp; Đạo diễn
        </a>

        <div class="flex flex-col sm:flex-row gap-8 lg:gap-10 items-start">

            {{-- Poster --}}
            <div class="person-poster mx-auto sm:mx-0">
                @if($person->photo)
                    <img src="{{ $person->photo }}" alt="{{ $person->name }}">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-20 h-20 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Text info --}}
            <div class="flex-1 min-w-0 text-white text-center sm:text-left">
                <h1 class="text-3xl lg:text-5xl font-extrabold leading-tight tracking-tight">
                    {{ $person->name }}
                </h1>

                {{-- badges --}}
                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-4">
                    @if($person->known_for)
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border {{ $roleStyle['color'] }}">
                            {{ $roleStyle['label'] }}
                        </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-white/10 text-gray-300 border border-white/10">
                        {{ $totalCredits }} tác phẩm
                    </span>
                </div>

                {{-- Biography preview --}}
                @if($bioText)
                    @php
                        $isLongBio = mb_strlen($bioText) > 350;
                    @endphp
                    <div class="mt-5 max-w-2xl text-sm text-gray-300 leading-relaxed" 
                         x-data="{ expanded: false }">
                         
                        <div class="transition-all duration-300 relative {{ $isLongBio ? 'line-clamp-4 mask-bottom' : '' }}"
                             :class="expanded ? '!line-clamp-none mask-none' : ''">
                             {!! nl2br(e($bioText)) !!}
                        </div>
                        
                        @if($isLongBio)
                            <button @click="expanded = !expanded"
                                    class="mt-2 text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1 group">
                                <span x-text="expanded ? 'Thu gọn' : 'Đọc thêm'"></span>
                                <svg class="w-3 h-3 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════ BODY (2 cols) ══════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-10 items-start">

        {{-- ── LEFT SIDEBAR ── --}}
        <aside class="person-sidebar lg:sticky lg:top-24">

            {{-- Social Links --}}
            @php
                $hasSocial = $person->imdb_id || $person->instagram_id || $person->twitter_id || $person->homepage;
            @endphp
            @if($hasSocial)
                <div class="flex items-center gap-3 mb-6">
                    @if($person->imdb_id)
                        <a href="https://www.imdb.com/name/{{ $person->imdb_id }}" target="_blank"
                           class="sidebar-social-btn inline-flex items-center justify-center p-0" title="IMDB">
                            <svg class="w-[20px] h-[20px]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14.31 9.588v.005c-.077-.048-.227-.07-.42-.07v4.815c.27 0 .44-.06.5-.17.062-.115.095-.404.095-.87v-2.97c0-.388-.013-.63-.04-.73a.347.347 0 0 0-.135-.01zM20 3H4a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM7.01 17H5.52V7h1.49v10zm4.37 0H9.96v-.56c-.2.23-.4.4-.615.51-.213.11-.437.162-.67.162-.27 0-.47-.077-.606-.23-.136-.154-.203-.39-.203-.708V9.847h1.395v6.568c0 .122.016.206.048.252.033.046.088.068.165.068.09 0 .2-.05.33-.154v-6.734h1.394V17zm4.312-.906c0 .354-.034.617-.102.79a.679.679 0 0 1-.352.39c-.17.086-.4.13-.694.13-.232 0-.43-.04-.6-.116a.919.919 0 0 1-.39-.344V17H12.16V7h1.395v2.99c.158-.2.34-.354.542-.46.202-.105.42-.157.654-.157.267 0 .488.065.66.196.174.13.29.3.348.503.058.204.088.558.088 1.065v2.52c0 .255-.004.4-.005.432zm3.913.332c0 .436-.084.754-.254.955-.17.2-.436.3-.8.3-.23 0-.43-.037-.6-.11a.909.909 0 0 1-.409-.333c-.108-.15-.178-.326-.21-.528-.033-.2-.05-.507-.05-.92V13.6h1.39v1.718c0 .36.01.577.03.65.02.074.075.11.165.11.1 0 .158-.044.178-.13.02-.085.03-.31.03-.672V13.6h1.39v.79c0 .393-.002.656-.006.788a2.944 2.944 0 0 1-.062.57 1.002 1.002 0 0 1-.234.42c-.11.11-.256.2-.44.263.205.07.35.186.437.35.087.163.13.44.13.83V17h-1.39v-.514c0-.33-.012-.535-.038-.614-.025-.08-.086-.12-.183-.12-.1 0-.158.04-.177.12a2.4 2.4 0 0 0-.028.473V17H17.21v-.384c0-.366.004-.596.012-.688.008-.09.034-.19.077-.296.044-.107.13-.197.26-.27.13-.07.305-.124.526-.16a.97.97 0 0 1-.076-.4zm-1.39-2.84v.56c0 .317.006.513.018.586.013.073.07.11.17.11.094 0 .15-.04.163-.12.013-.08.02-.284.02-.61v-.525h-.37z"/>
                            </svg>
                        </a>
                    @endif
                    @if($person->instagram_id)
                        <a href="https://www.instagram.com/{{ $person->instagram_id }}" target="_blank"
                           class="sidebar-social-btn inline-flex items-center justify-center p-0" title="Instagram">
                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="2" y="2" width="20" height="20" rx="5"/>
                                <circle cx="12" cy="12" r="4"/>
                                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                            </svg>
                        </a>
                    @endif
                    @if($person->twitter_id)
                        <a href="https://twitter.com/{{ $person->twitter_id }}" target="_blank"
                           class="sidebar-social-btn inline-flex items-center justify-center p-0" title="Twitter / X">
                            <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.262 5.636 5.901-5.636zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                    @endif
                    @if($person->homepage)
                        <a href="{{ $person->homepage }}" target="_blank"
                           class="sidebar-social-btn inline-flex items-center justify-center p-0" title="Website">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Personal Information --}}
            <h3 class="text-[13px] font-extrabold text-slate-900 uppercase tracking-widest mb-1">
                Thông tin cá nhân
            </h3>

            <div class="divide-y divide-slate-100">

                @if($person->known_for)
                    <div class="sidebar-info-row">
                        <span class="sidebar-info-label">Nổi tiếng với</span>
                        <span class="sidebar-info-value">{{ $roleStyle['label'] }}</span>
                    </div>
                @endif

                <div class="sidebar-info-row">
                    <span class="sidebar-info-label">Số tác phẩm</span>
                    <span class="sidebar-info-value">{{ $totalCredits }}</span>
                </div>

                @if($person->gender)
                    <div class="sidebar-info-row">
                        <span class="sidebar-info-label">Giới tính</span>
                        <span class="sidebar-info-value">{{ $person->gender_label }}</span>
                    </div>
                @endif

                @if($person->date_of_birth)
                    <div class="sidebar-info-row">
                        <span class="sidebar-info-label">Ngày sinh</span>
                        <span class="sidebar-info-value">
                            {{ $person->date_of_birth->translatedFormat('d F, Y') }}
                            @if(!$person->date_of_death)
                                <span class="text-indigo-500 font-semibold">({{ $person->date_of_birth->age }} tuổi)</span>
                            @endif
                        </span>
                    </div>
                @endif

                @if($person->date_of_death)
                    <div class="sidebar-info-row">
                        <span class="sidebar-info-label">Ngày mất</span>
                        <span class="sidebar-info-value text-red-500">
                            {{ $person->date_of_death->translatedFormat('d F, Y') }}
                        </span>
                    </div>
                @endif

                @if($person->place_of_birth)
                    <div class="sidebar-info-row">
                        <span class="sidebar-info-label">Nơi sinh</span>
                        <span class="sidebar-info-value">{{ $person->place_of_birth }}</span>
                    </div>
                @endif

                @if($person->nationality)
                    <div class="sidebar-info-row">
                        <span class="sidebar-info-label">Quốc tịch</span>
                        <span class="sidebar-info-value">{{ $person->nationality }}</span>
                    </div>
                @endif

                @if(!empty($person->also_known_as))
                    <div class="sidebar-info-row">
                        <span class="sidebar-info-label">Còn được biết với tên</span>
                        <div class="sidebar-info-value space-y-0.5">
                            @foreach($person->also_known_as as $alias)
                                <div>{{ $alias }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </aside>

        {{-- ── MAIN CONTENT ── --}}
        <div class="flex-1 min-w-0 space-y-14">

            {{-- ── "Known For" Gallery ── --}}
            @if($knownForMovies->isNotEmpty())
                <section>
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-1 h-6 rounded-full bg-indigo-500 inline-block"></span>
                        <h2 class="credits-section-title">Nổi tiếng với</h2>
                    </div>
                    <div class="known-for-track">
                        @foreach($knownForMovies as $kfm)
                            <a href="{{ route('movies.show', $kfm) }}" class="known-for-card">
                                <div class="known-for-card-img">
                                    @if($kfm->poster)
                                        <img src="{{ $kfm->poster }}" alt="{{ $kfm->title }}" loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <p class="known-for-card-title">{{ $kfm->title }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ── Acting Credits (by year) ── --}}
            @if($actedByYear->isNotEmpty())
                <section>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-1 h-6 rounded-full bg-sky-500 inline-block"></span>
                        <h2 class="credits-section-title">
                            Diễn xuất
                            <span class="text-slate-400 font-normal text-base">({{ $actedMovies->count() }})</span>
                        </h2>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
                        @foreach($actedByYear as $year => $yearMovies)
                            @foreach($yearMovies as $loopIdx => $movie)
                                <a href="{{ route('movies.show', $movie) }}" class="credit-row group">
                                    {{-- Year badge (only on first of group) --}}
                                    <div class="w-14 shrink-0 text-right">
                                        @if($loopIdx === 0)
                                            <span class="credits-year-badge {{ $year === 'TBA' ? 'tba' : '' }}">
                                                {{ $year }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="credit-dot"></div>

                                    {{-- Poster --}}
                                    <div class="credit-poster">
                                        @if($movie->poster)
                                            <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" loading="lazy">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="credit-title truncate">{{ $movie->title }}</div>
                                        @if($movie->pivot->character_name)
                                            <div class="credit-char">vai {{ $movie->pivot->character_name }}</div>
                                        @endif
                                    </div>

                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-400 shrink-0 transition-colors"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ── Crew sections (Director / Writer / Producer) ── --}}
            @foreach($crewedByJob as $job => $yearGroups)
                @php
                    $jobColor = ['Director'=>'bg-amber-400','Writer'=>'bg-purple-500','Producer'=>'bg-green-500'][$job] ?? 'bg-slate-400';
                    $totalJobMovies = $yearGroups->flatten()->count();
                @endphp
                <section>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-1 h-6 rounded-full {{ $jobColor }} inline-block"></span>
                        <h2 class="credits-section-title">
                            {{ $jobLabels[$job] ?? $job }}
                            <span class="text-slate-400 font-normal text-base">({{ $totalJobMovies }})</span>
                        </h2>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
                        @foreach($yearGroups as $year => $movies)
                            @foreach($movies as $loopIdx => $movie)
                                <a href="{{ route('movies.show', $movie) }}" class="credit-row group">
                                    <div class="w-14 shrink-0 text-right">
                                        @if($loopIdx === 0)
                                            <span class="credits-year-badge {{ $year === 'TBA' ? 'tba' : '' }}">
                                                {{ $year }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="credit-dot"></div>
                                    <div class="credit-poster">
                                        @if($movie->poster)
                                            <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" loading="lazy">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="credit-title truncate">{{ $movie->title }}</div>
                                        @if($movie->release_date)
                                            <div class="credit-char">{{ $movie->release_date->format('d/m/Y') }}</div>
                                        @endif
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-400 shrink-0 transition-colors"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </section>
            @endforeach

            {{-- Empty state --}}
            @if($actedByYear->isEmpty() && $crewedByJob->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                    </svg>
                    <p class="text-slate-400 text-sm">Chưa có tác phẩm nào được liên kết</p>
                </div>
            @endif

        </div>{{-- /main --}}
    </div>{{-- /cols --}}
</div>{{-- /container --}}

</x-app-layout>
