@props(['movie', 'myMood', 'myTone'])

<div x-data="vibeModal()" 
     @open-vibe-modal.window="open = true"
     class="relative z-50">
     
    {{-- Modal Overlay --}}
    <div x-show="open" 
         style="display: none;"
         x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-black/60 z-[200] flex items-center justify-center p-4">
         
        {{-- Modal Content --}}
        <div @click.outside="open = false" 
             class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden flex flex-col relative"
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
             
            {{-- Header --}}
            <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                <div>
                    <h3 class="text-3xl font-black text-[#022541] mb-1">Cảm xúc</h3>
                    <p class="text-base text-slate-500 italic font-medium">Bạn cảm thấy bộ phim này như thế nào?</p>
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-black transition-colors rounded-full hover:bg-gray-200 p-2 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Emoji Grid --}}
            <div class="flex-1 overflow-y-auto px-8 pb-24 scrollbar-hide">
                <div class="space-y-0 divide-y divide-gray-200 border-t border-b border-gray-200 pb-2 transition-opacity" :class="isLoading ? 'opacity-50 pointer-events-none' : ''">
                    <template x-for="(group, key) in moodGroups" :key="key">
                        <div class="py-4 flex items-center min-h-[72px]">
                            <div class="w-28 shrink-0 font-bold text-[#022541] text-base" x-text="key"></div>
                            <div class="flex-1 flex flex-wrap gap-4 items-center">
                                <template x-for="emj in group" :key="emj">
                                    <button @click="toggleMood(emj)" 
                                            class="w-11 h-11 flex items-center justify-center transition-all duration-300 relative rounded-full group"
                                            :class="currentMood === emj ? 'scale-125 z-10 bg-yellow-100 ring-4 ring-yellow-100 shadow-sm' : 'hover:scale-125 hover:z-10 bg-transparent'">
                                        <img :src="getTwemojiUrl(emj)" class="w-9 h-9 rounded-full drop-shadow-sm" :alt="emj">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Floating Done Button --}}
            <div class="absolute bottom-6 right-8 z-30">
                <button @click="open = false" 
                        class="px-6 py-3 bg-[#022541] hover:bg-[#03345a] text-white font-bold rounded-full shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all flex items-center gap-2 border border-[#022541]/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Hoàn tất
                </button>
            </div>
             
        </div>
    </div>

    {{-- Success Toast --}}
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
         style="display: none;"
         class="fixed bottom-6 right-6 z-[300] w-[320px] max-w-[calc(100vw-2rem)] rounded-xl overflow-hidden shadow-2xl pointer-events-auto bg-gradient-to-r from-emerald-600 to-green-600 ring-1 ring-black/10">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-base font-bold text-white leading-tight mb-0.5">Thành công!</p>
                    <p class="text-[13px] text-green-50 font-medium">Đã lưu thành công cảm xúc của bạn.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alpine Component --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('vibeModal', () => ({
            open: false,
            showToast: false,
            isLoading: false,
            currentMood: @json($myMood ?? null),
            
            moodGroups: {
                'Vui vẻ': ['😃', '🤭', '🥲', '😂', '🤣', '😍'],
                'Hứng thú': ['🥱', '😐', '😵‍💫', '🤨', '🤔', '🤯'],
                'Bất ngờ': ['😮', '😳', '🫢', '😨'],
                'Buồn bã': ['☹️', '😔', '😣', '😫', '😢', '😭'],
                'Khó chịu': ['🫤', '😬', '🤢', '🤮'],
                'Sợ hãi': ['😧', '😰', '🫣', '😱'],
                'Tức giận': ['😒', '😖', '😡', '🤬'],
            },
            
            getTwemojiUrl(emoji) {
                if (!emoji) return '';
                const codePoints = Array.from(emoji).map(c => c.codePointAt(0).toString(16));
                const cleanCodePoints = codePoints.filter(cp => cp !== 'fe0f').join('-');
                return `https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/svg/${cleanCodePoints}.svg`;
            },
            
            async toggleMood(emoji) {
                @guest window.location.href = '{{ route('login') }}'; return; @endguest

                if (this.isLoading) return;
                this.isLoading = true;

                this.currentMood = (this.currentMood === emoji) ? null : emoji;
                await this.syncWithServer({ mood: this.currentMood ?? '' });

                this.isLoading = false;
            },
            
            async syncWithServer(payload) {
                try {
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';
                    
                    const res = await fetch('{{ route('movies.vibe.update', ['movie' => $movie->slug]) }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': csrfToken 
                        },
                        body: JSON.stringify(payload)
                    });
                    
                    if (!res.ok) {
                        const errorText = await res.text();
                        console.error('Server error:', res.status, errorText);
                        alert('Có lỗi xảy ra: ' + res.status + '. Vui lòng tải lại trang và thử lại.');
                        return;
                    }
                    
                    const data = await res.json();
                    if(data.success) {
                        this.currentMood = data.mood;
                        const moods = Array.isArray(data.top_moods) ? data.top_moods : Object.values(data.top_moods);
                        window.dispatchEvent(new CustomEvent('vibes-updated', { detail: { top_moods: moods, mood: data.mood } }));
                        
                        // Close modal and show success toast
                        this.open = false;
                        this.showToast = true;
                        setTimeout(() => { this.showToast = false; }, 3500);
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể lưu cảm nhận.'));
                    }
                } catch(e) {
                    console.error("Failed to update vibe", e);
                    alert("Lỗi kết nối mạng hoặc lỗi hệ thống. Vui lòng thử lại!");
                }
            }
        }));
    });
</script>
