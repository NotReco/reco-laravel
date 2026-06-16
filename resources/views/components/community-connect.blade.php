<div x-data="{ open: false }" 
     @ai-opened.window="open = false"
     class="hidden md:block fixed bottom-24 right-4 md:bottom-24 md:right-6 z-[9980]">
    {{-- Panel --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="absolute bottom-[calc(100%+1.25rem)] right-0 w-[calc(100vw-2rem)] max-w-[320px] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden"
         style="display: none;"
         @click.outside="open = false">
        <div class="bg-gradient-to-r from-sky-500 to-indigo-600 p-4 text-white flex justify-between items-center shadow-sm z-10 relative">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm border border-white/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm leading-tight">Tham gia cộng đồng</h3>
                    <p class="text-[11px] text-sky-100/90 flex items-center gap-1 mt-0.5"><span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span> Giao lưu & Thảo luận</p>
                </div>
            </div>
            <button @click="open = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors text-white" aria-label="Đóng">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5 bg-[#f8fafc]">
            <div class="space-y-3">
                <a href="https://zalo.me/g/5o5ee8fot0igcgsrooht" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3.5 px-4 py-3 bg-white border border-gray-100 shadow-sm hover:border-blue-200 hover:bg-blue-50 hover:shadow-md text-gray-700 hover:text-blue-700 rounded-2xl transition-all font-semibold group">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M22.048 11.233c0-4.99-4.886-9.034-10.912-9.034C5.112 2.2 0 6.244 0 11.233c0 2.65 1.4 5.05 3.633 6.643V22.25c0 .542.617.857 1.074.562l3.666-2.355c.91.26 1.875.405 2.875.405 6.026 0 10.912-4.044 10.912-9.034h-.112zm-6.935 3.42H10.15c-.32 0-.578-.26-.578-.577s.26-.577.578-.577h2.443l-2.32-2.833a.604.604 0 0 1-.068-.168v-1.253c0-.32.26-.578.578-.578h4.963c.32 0 .578.26.578.578s-.26.578-.578.578h-2.443l2.32 2.833c.044.054.067.112.067.18v1.25c.002.28-.27.545-.578.545z"/></svg>
                    </div>
                    <span>Nhóm Zalo</span>
                </a>
                <a href="https://www.facebook.com/groups/recodb" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3.5 px-4 py-3 bg-white border border-gray-100 shadow-sm hover:border-blue-200 hover:bg-[#f0f5fa] hover:shadow-md text-gray-700 hover:text-[#1877f2] rounded-2xl transition-all font-semibold group">
                    <div class="w-10 h-10 rounded-full bg-[#1877f2] text-white flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </div>
                    <span>Nhóm Facebook</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Button --}}
    <button @click="open = !open; if(open) $dispatch('community-opened')" 
            class="w-14 h-14 bg-gradient-to-br from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center relative group"
            aria-label="Kết nối cộng đồng">
        {{-- Pulse Effect --}}
        <span class="absolute inset-0 rounded-full bg-sky-500 opacity-20 group-hover:opacity-40 group-hover:animate-ping"></span>
        
        <svg x-show="!open" class="w-7 h-7 relative z-10 transition-transform origin-center group-hover:animate-community-wiggle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
        <svg x-show="open" style="display: none;" class="w-7 h-7 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <style>
        @keyframes community-wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-8deg); }
            75% { transform: rotate(8deg); }
        }
        .group:hover .group-hover\:animate-community-wiggle {
            animation: community-wiggle 0.5s ease-in-out infinite;
        }
    </style>
</div>
