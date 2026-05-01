<x-app-layout>
    <x-slot:title>Trung tâm Sự kiện</x-slot:title>

    <style>
        /* Animation keyframes for the event hub */
        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; filter: brightness(1); }
            50% { opacity: 0.8; filter: brightness(1.2) drop-shadow(0 0 10px rgba(99,102,241,0.5)); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Glassmorphism dark panels */
        .glass-panel-dark {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Custom scrollbar for events */
        .event-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .event-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 4px;
        }
        .event-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.5);
            border-radius: 4px;
        }
        .event-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.8);
        }

        /* Confetti Canvas */
        #confetti-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
        }
    </style>

    <div class="min-h-screen relative overflow-hidden bg-slate-900 text-slate-200" x-data="eventHub()">
        {{-- Background Effects --}}
        <div class="fixed inset-0 z-0 pointer-events-none">
            {{-- Dark grid --}}
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAzOGMtMS4xIDAtMi0uOS0yLTJzLjktMiAyLTIgMiAuOSAyIDItLjkgMi0yIDJ6bS0xMC04Yy0xLjEgMC0yLS45LTItMnMuOS0yIDItMiAyIC45IDIgMi0uOSAyLTIgMnptMTAgOGMtMS4xIDAtMi0uOS0yLTJzLjktMiAyLTIgMiAuOSAyIDItLjkgMi0yIDJ6IiBmaWxsPSIjMzMzIiBmaWxsLW9wYWNpdHk9IjAuMSIvPjwvZz48L3N2Zz4=')] opacity-20"></div>
            
            {{-- Glowing Orbs --}}
            <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-indigo-600/20 blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40vw] h-[40vw] rounded-full bg-fuchsia-600/20 blur-[120px]"></div>
            <div class="absolute top-[40%] left-[60%] w-[30vw] h-[30vw] rounded-full bg-sky-500/10 blur-[100px]"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            
            {{-- Header Section --}}
            <div class="text-center mb-16 animate-float">
                <div class="inline-flex items-center justify-center p-4 rounded-3xl bg-indigo-500/10 border border-indigo-500/20 mb-6 shadow-[0_0_30px_rgba(99,102,241,0.2)]">
                    <svg class="w-12 h-12 text-indigo-400 drop-shadow-[0_0_10px_rgba(99,102,241,0.8)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-display font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-purple-300 to-fuchsia-300 drop-shadow-lg tracking-tight mb-4 py-2 leading-normal uppercase">
                    Trung Tâm Sự Kiện
                </h1>
                <p class="text-lg text-indigo-200/70 max-w-2xl mx-auto font-medium">
                    Hoàn thành các thử thách đặc biệt để mở khóa những danh hiệu và khung avatar độc quyền dành riêng cho bạn.
                </p>
            </div>

            {{-- Quests Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @forelse($quests as $quest)
                    @php
                        $progress = $quest->userProgress->first();
                        $isCompleted = $progress?->isCompleted() ?? false;
                        $isRewarded = $progress?->isRewarded() ?? false;
                        $percent = $progress ? $progress->percentageFor($quest) : 0;
                        $currentVal = $progress ? $progress->progress : 0;
                        $canClaim = $isCompleted && !$isRewarded;
                    @endphp

                    <div class="relative glass-panel-dark rounded-3xl overflow-hidden group transition-all duration-500 hover:shadow-[0_0_40px_rgba(99,102,241,0.15)] hover:-translate-y-2 border-t border-l border-white/10"
                         :class="{ 'opacity-70 scale-[0.98] grayscale-[30%]': claimedQuests.includes({{ $quest->id }}) || '{{ $isRewarded }}' == '1' }">
                        
                        {{-- Top Highlight Line --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-50 group-hover:opacity-100 transition-opacity"></div>

                        {{-- Glow Behind Card if can claim --}}
                        @if($canClaim)
                            <div class="absolute inset-0 bg-indigo-500/10 animate-pulse pointer-events-none" x-show="!claimedQuests.includes({{ $quest->id }})"></div>
                        @endif

                        <div class="p-6 md:p-8">
                            {{-- Icon & Title --}}
                            <div class="flex items-start gap-4 mb-6">
                                <div class="shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center shadow-inner relative overflow-hidden
                                    {{ $isCompleted ? 'bg-gradient-to-br from-indigo-500 to-fuchsia-600' : 'bg-slate-800 border border-slate-700' }}">
                                    @if($isCompleted)
                                        <div class="absolute inset-0 bg-white/20 blur-md"></div>
                                    @endif
                                    @if($quest->reward_type === 'title')
                                        <svg class="w-7 h-7 relative z-10 {{ $isCompleted ? 'text-white drop-shadow-md' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    @else
                                        <svg class="w-7 h-7 relative z-10 {{ $isCompleted ? 'text-white drop-shadow-md' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-slate-100 truncate mb-1" title="{{ $quest->name }}">{{ $quest->name }}</h3>
                                    <p class="text-sm text-slate-400 line-clamp-2 leading-relaxed" title="{{ $quest->description }}">{{ $quest->description }}</p>
                                </div>
                            </div>

                            {{-- Reward Preview Panel --}}
                            <div class="bg-slate-900/50 rounded-2xl p-4 border border-white/5 mb-6">
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Phần thưởng</p>
                                <div class="flex justify-center items-center h-16">
                                    @if($quest->reward_type === 'title' && $quest->rewardTitle)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-extrabold tracking-wide border shadow-lg"
                                              style="color: {{ $quest->rewardTitle->color_hex }}; border-color: {{ $quest->rewardTitle->color_hex }}60; background-color: {{ $quest->rewardTitle->color_hex }}15; text-shadow: 0 0 10px {{ $quest->rewardTitle->color_hex }}40;">
                                            {{ $quest->rewardTitle->name }}
                                        </span>
                                    @elseif($quest->reward_type === 'frame' && $quest->rewardFrame)
                                        <div class="relative w-16 h-16 shrink-0 group-hover:scale-110 transition-transform duration-500">
                                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full blur-md"></div>
                                            <img src="{{ Storage::url($quest->rewardFrame->image_path) }}" class="absolute inset-0 w-full h-full object-contain filter drop-shadow-xl" alt="{{ $quest->rewardFrame->name }}">
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-600 font-medium">Bí ẩn</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress / Action --}}
                            <div class="relative">
                                {{-- 1. Already Claimed --}}
                                <div x-show="claimedQuests.includes({{ $quest->id }}) || '{{ $isRewarded }}' == '1'" style="display: {{ $isRewarded ? 'block' : 'none' }}">
                                    <div class="w-full py-3 rounded-xl bg-slate-800/80 border border-slate-700/50 text-center flex items-center justify-center gap-2 text-slate-400 font-bold">
                                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Đã nhận thưởng
                                    </div>
                                </div>

                                {{-- 2. Can Claim --}}
                                <div x-show="!claimedQuests.includes({{ $quest->id }}) && '{{ $canClaim }}' == '1'" style="display: {{ $canClaim ? 'block' : 'none' }}">
                                    <button @click="claimReward({{ $quest->id }}, $event.target)" 
                                            class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-fuchsia-600 text-white font-bold text-[15px] hover:from-indigo-400 hover:to-fuchsia-500 transition-all shadow-[0_0_20px_rgba(99,102,241,0.4)] relative overflow-hidden group/btn active:scale-95">
                                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent group-hover/btn:animate-[shimmer_1.5s_infinite]"></div>
                                        <span class="relative z-10 flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            Nhận Thưởng
                                        </span>
                                    </button>
                                </div>

                                {{-- 3. In Progress --}}
                                <div x-show="!claimedQuests.includes({{ $quest->id }}) && !('{{ $isCompleted }}' == '1')" style="display: {{ !$isCompleted ? 'block' : 'none' }}">
                                    <div class="flex justify-between text-[13px] font-bold mb-2">
                                        <span class="text-indigo-300">Tiến độ</span>
                                        <span class="text-indigo-200">{{ min($currentVal, $quest->target_value) }} <span class="text-slate-500">/ {{ $quest->target_value }}</span></span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-3 border border-slate-700 overflow-hidden shadow-inner relative">
                                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-600 to-fuchsia-500 rounded-full transition-all duration-1000 ease-out" 
                                             style="width: {{ $percent }}%">
                                            {{-- Animated strip inside progress --}}
                                            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImEiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTTAgNDBsNDAtNDBIMjBMMCAyMHptNDAgMEwyMCA0MGgyMHoiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2EpIi8+PC9zdmc+')] animate-[shimmer_2s_linear_infinite]"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 glass-panel-dark rounded-3xl p-16 text-center border-dashed border-2 border-slate-700/50">
                        <div class="w-20 h-20 rounded-full bg-slate-800/50 border border-slate-700 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-200 mb-2">Chưa có sự kiện nào</h3>
                        <p class="text-slate-400">Các sự kiện và thử thách mới đang được thiết kế. Vui lòng quay lại sau nhé!</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <canvas id="confetti-canvas"></canvas>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('eventHub', () => ({
                    claimedQuests: [],
                    
                    async claimReward(questId, btnEl) {
                        const originalHtml = btnEl.innerHTML;
                        btnEl.innerHTML = `<svg class="animate-spin w-5 h-5 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang nhận...`;
                        btnEl.disabled = true;

                        try {
                            const res = await fetch(`/events/${questId}/claim`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                }
                            });
                            const data = await res.json();
                            
                            if (data.success) {
                                // Add to claimed array to trigger UI update
                                this.claimedQuests.push(questId);
                                
                                // Fire confetti
                                this.fireConfetti(btnEl);
                                
                                // Optional: Play sound
                                // const audio = new Audio('/sounds/success.mp3');
                                // audio.play();
                            } else {
                                alert(data.message || 'Có lỗi xảy ra.');
                                btnEl.innerHTML = originalHtml;
                                btnEl.disabled = false;
                            }
                        } catch (e) {
                            console.error(e);
                            alert('Lỗi kết nối. Vui lòng thử lại sau.');
                            btnEl.innerHTML = originalHtml;
                            btnEl.disabled = false;
                        }
                    },

                    fireConfetti(element) {
                        const rect = element.getBoundingClientRect();
                        const x = (rect.left + rect.width / 2) / window.innerWidth;
                        const y = (rect.top + rect.height / 2) / window.innerHeight;

                        var duration = 3000;
                        var animationEnd = Date.now() + duration;
                        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

                        function randomInRange(min, max) {
                            return Math.random() * (max - min) + min;
                        }

                        var interval = setInterval(function() {
                            var timeLeft = animationEnd - Date.now();

                            if (timeLeft <= 0) {
                                return clearInterval(interval);
                            }

                            var particleCount = 50 * (timeLeft / duration);
                            confetti(Object.assign({}, defaults, { 
                                particleCount, 
                                origin: { x: x, y: y },
                                colors: ['#6366f1', '#a855f7', '#ec4899', '#ffffff', '#eab308']
                            }));
                        }, 250);
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>
