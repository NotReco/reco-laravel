<div x-data="aiAssistantWidget()"
     @community-opened.window="open = false"
     class="fixed bottom-6 right-4 md:bottom-6 md:right-6 z-[9990]">
    
    <!-- Chat Popup -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 md:translate-y-0 md:translate-x-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 md:translate-x-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 md:translate-x-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 md:translate-y-0 md:translate-x-4 scale-95"
         class="absolute bottom-[calc(100%+1rem)] right-0 md:bottom-0 md:right-[calc(100%+1.5rem)] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden w-[calc(100vw-2rem)] sm:w-[360px] flex flex-col origin-bottom-right h-[520px] max-h-[calc(100vh-120px)]"
         x-cloak>
         
        <!-- Header -->
        <div class="bg-gradient-to-r from-sky-500 to-indigo-600 p-4 text-white flex justify-between items-center shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm border border-white/20">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 8V4H8" />
                        <rect width="16" height="12" x="4" y="8" rx="2" />
                        <path d="M2 14h2" />
                        <path d="M20 14h2" />
                        <path d="M15 13v2" />
                        <path d="M9 13v2" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm leading-tight">Trợ lý RecoDB</h3>
                    <p class="text-[11px] text-sky-100/90 flex items-center gap-1 mt-0.5"><span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span> Luôn sẵn sàng</p>
                </div>
            </div>
            <button @click="open = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors text-white" aria-label="Đóng">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 p-4 overflow-y-auto bg-[#f8fafc] flex flex-col gap-4 custom-scrollbar" id="ai-messages-container">
            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex flex-col max-w-[85%]" :class="msg.source === 'user' ? 'self-end items-end' : 'self-start items-start'">
                    <span class="text-[10px] text-gray-400 mb-1 px-1" x-text="msg.source === 'user' ? 'Bạn' : 'Trợ lý AI'"></span>
                    <div class="px-4 py-2.5 rounded-2xl text-[13px] leading-relaxed shadow-sm" 
                         :class="msg.source === 'user' ? 'bg-sky-500 text-white rounded-br-sm' : 'bg-white border border-gray-100 text-gray-800 rounded-bl-sm'">
                        <!-- Use textContent safely via x-text -->
                        <div class="whitespace-pre-wrap" x-text="msg.text"></div>
                    </div>
                    
                    <!-- Suggested Items Cards -->
                    <template x-if="msg.suggested_items && msg.suggested_items.length > 0">
                        <div class="mt-2 flex flex-col gap-2 w-full pr-2">
                            <template x-for="item in msg.suggested_items" :key="item.id">
                                <a :href="item.url" target="_blank" class="flex gap-2 p-2 bg-white rounded-lg border border-gray-200 hover:border-sky-300 hover:shadow-md transition-all group max-w-full">
                                    <template x-if="item.poster">
                                        <div class="relative shrink-0 w-14 h-20">
                                            <img :src="item.poster" :alt="item.title" 
                                                 class="absolute inset-0 w-full h-full object-cover rounded shadow-sm bg-gray-100"
                                                 x-on:error="$event.target.style.display='none'; $event.target.nextElementSibling.classList.remove('hidden'); $event.target.nextElementSibling.classList.add('flex');">
                                            <div class="hidden absolute inset-0 bg-gray-100 items-center justify-center rounded shadow-sm text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!item.poster">
                                        <div class="w-14 h-20 bg-gray-100 flex items-center justify-center rounded shadow-sm shrink-0 text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    </template>
                                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                                        <h4 class="text-xs font-bold text-gray-800 truncate group-hover:text-sky-600 transition-colors" x-text="item.title"></h4>
                                        <div class="text-[10px] text-gray-500 mt-0.5 truncate">
                                            <span x-text="item.type === 'movie' ? 'Phim lẻ' : 'Phim bộ'"></span>
                                            <template x-if="item.year">
                                                <span x-text="' • ' + item.year"></span>
                                            </template>
                                        </div>
                                        <div class="text-[10px] text-gray-400 truncate mt-0.5" x-text="item.genres"></div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
            
            <!-- Loading Indicator -->
            <div x-show="loading" class="self-start max-w-[85%] flex flex-col items-start mt-1">
                <span class="text-[10px] text-gray-400 mb-1 px-1">Trợ lý AI đang gõ...</span>
                <div class="px-4 py-3 bg-white border border-gray-100 shadow-sm rounded-2xl rounded-bl-sm flex gap-1.5 items-center">
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Carousel -->
        <div class="bg-white border-t border-gray-100 shrink-0 select-none flex items-center justify-between px-4 py-2.5 gap-2">
            <!-- Nút cuộn trái -->
            <button type="button" 
                    @click="prevAction()" 
                    class="w-9 h-9 bg-gray-50 hover:bg-sky-50 border border-gray-200 rounded-full shadow-sm flex items-center justify-center text-gray-500 hover:text-sky-500 transition-colors shrink-0 hover:scale-105"
                    aria-label="Câu hỏi trước">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>

            <!-- Câu hỏi gợi ý hiện tại -->
            <button type="button"
                    @click="sendQuickAction(quickActions[currentActionIndex])"
                    :disabled="loading"
                    class="flex-1 min-w-0 px-4 py-2.5 bg-gray-50 hover:bg-sky-50 hover:text-sky-600 text-gray-600 text-xs font-semibold rounded-full transition-all border border-gray-200 hover:border-sky-200 shadow-sm truncate text-center disabled:opacity-40 disabled:cursor-not-allowed"
                    :title="quickActions[currentActionIndex]">
                <span x-text="quickActions[currentActionIndex]"></span>
            </button>

            <!-- Nút mũi tên phải -->
            <button type="button" 
                    @click="nextAction()" 
                    class="w-9 h-9 bg-gray-50 hover:bg-sky-50 border border-gray-200 rounded-full shadow-sm flex items-center justify-center text-gray-500 hover:text-sky-500 transition-colors shrink-0 hover:scale-105"
                    aria-label="Câu hỏi tiếp theo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white border-t border-gray-100 shrink-0">
            <form @submit.prevent="sendMessage" class="relative flex items-center">
                <input x-model="input" 
                       type="text" 
                       placeholder="Hỏi trợ lý AI..." 
                       class="w-full pl-4 pr-12 py-2.5 bg-gray-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-all placeholder-gray-400 shadow-sm"
                       :disabled="loading">
                <button type="submit" 
                        class="absolute right-1.5 w-8.5 h-8.5 flex items-center justify-center bg-sky-500 hover:bg-sky-600 text-white rounded-full transition-colors disabled:opacity-40 disabled:cursor-not-allowed shadow-md"
                        style="width: 34px; height: 34px;"
                        :disabled="!input.trim() || loading">
                    <svg class="w-4 h-4 transform -translate-x-0.5 translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="m22 2-7 20-4-9-9-4Z" />
                        <path d="M22 2 11 13" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Toggle Button -->
    <button @click="open = !open; if(open) $dispatch('ai-opened')" 
            class="relative w-14 h-14 bg-gradient-to-br from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center group">
        
        <!-- Pulse ring behind -->
        <div class="absolute inset-0 rounded-full bg-sky-500 animate-ping opacity-20 group-hover:opacity-40"></div>
        
        <!-- Icon Robot -->
        <svg x-show="!open" class="w-7 h-7 z-10 transition-transform origin-center group-hover:animate-wiggle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 8V4H8" />
            <rect width="16" height="12" x="4" y="8" rx="2" />
            <path d="M2 14h2" />
            <path d="M20 14h2" />
            <path d="M15 13v2" />
            <path d="M9 13v2" />
        </svg>
        
        <!-- Icon Close -->
        <svg x-show="open" x-cloak class="w-6 h-6 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('aiAssistantWidget', () => ({
                open: false,
                loading: false,
                input: '',
                currentActionIndex: 0,
                messages: [
                    { source: 'ai', text: 'Chào bạn! Mình là trợ lý AI của RecoDB. Mình có thể giúp gì cho bạn hôm nay?', suggested_items: [] }
                ],
                quickActions: [
                    'Gợi ý phim cho tôi',
                    'Phim đang nổi bật',
                    'Tôi nên xem gì hôm nay?',
                    'Cách viết review hay?'
                ],
                recentSuggestedItems: [],
                init() {
                    this.$watch('messages', () => {
                        this.scrollToBottom();
                    });
                    try {
                        const stored = sessionStorage.getItem('reco_recent_suggested_items');
                        if (stored) this.recentSuggestedItems = JSON.parse(stored);
                    } catch (e) {}
                },
                scrollToBottom() {
                    setTimeout(() => {
                        const container = document.getElementById('ai-messages-container');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    }, 50);
                },
                prevAction() {
                    this.currentActionIndex = (this.currentActionIndex - 1 + this.quickActions.length) % this.quickActions.length;
                },
                nextAction() {
                    this.currentActionIndex = (this.currentActionIndex + 1) % this.quickActions.length;
                },
                sendQuickAction(action) {
                    this.input = action;
                    this.sendMessage();
                },
                async sendMessage() {
                    const text = this.input.trim();
                    if (!text || this.loading) return;

                    this.messages.push({ source: 'user', text: text });
                    this.input = '';
                    this.loading = true;

                    try {
                        const response = await fetch('/api/ai-assistant', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ 
                                message: text,
                                recent_suggested_items: this.recentSuggestedItems
                            })
                        });

                        const data = await response.json();
                        
                        if (response.ok && data.message) {
                            this.messages.push({ 
                                source: 'ai', 
                                text: data.message,
                                suggested_items: data.suggested_items || [],
                                meta_source: data.source || null,
                                fallback: data.fallback || false,
                            });
                            
                            // Only track non-fallback items to avoid remembering empty responses
                            if (!data.fallback && data.suggested_items && Array.isArray(data.suggested_items) && data.suggested_items.length > 0) {
                                const newItems = [...this.recentSuggestedItems, ...data.suggested_items];
                                const uniqueMap = new Map();
                                newItems.forEach(item => {
                                    if (item.type && item.id) {
                                        uniqueMap.set(item.type + '_' + item.id, {type: item.type, id: item.id});
                                    }
                                });
                                let uniqueItems = Array.from(uniqueMap.values());
                                if (uniqueItems.length > 20) {
                                    uniqueItems = uniqueItems.slice(uniqueItems.length - 20);
                                }
                                this.recentSuggestedItems = uniqueItems;
                                sessionStorage.setItem('reco_recent_suggested_items', JSON.stringify(uniqueItems));
                            }
                        } else {
                            // Non-ok response (429, 500, etc.) – always no cards
                            this.messages.push({
                                source: 'system',
                                text: data.message || 'Trợ lý AI hiện chưa khả dụng. Bạn có thể thử lại sau.',
                                suggested_items: [],
                                fallback: true,
                            });
                        }
                    } catch (error) {
                        // Network/parse error – always no cards
                        this.messages.push({
                            source: 'system',
                            text: 'Đã có lỗi xảy ra khi kết nối máy chủ. Bạn có thể thử lại sau.',
                            suggested_items: [],
                            fallback: true,
                        });
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Ẩn scrollbar hoàn toàn cho quick actions */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes robot-wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-8deg); }
            75% { transform: rotate(8deg); }
        }
        .group:hover .group-hover\:animate-wiggle {
            animation: robot-wiggle 0.5s ease-in-out infinite;
        }
    </style>
</div>
