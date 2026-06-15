@props(['mode' => 'banner'])

@if($mode === 'banner')
<div class="block lg:hidden mb-6 bg-sky-500/10 border border-sky-500/20 rounded-xl p-4">
    <div class="flex items-start gap-3">
        <div class="mt-0.5 text-sky-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="text-sm text-sky-100/90 leading-relaxed">
            <span class="font-semibold text-sky-300">Chế độ xem trên thiết bị di động:</span>
            Bạn vẫn có thể xem dashboard, duyệt báo cáo và thao tác nhanh. Tuy nhiên, một số chức năng quản trị hoặc biểu mẫu phức tạp sẽ bị ẩn để đảm bảo an toàn dữ liệu. Khuyên dùng máy tính hoặc máy tính bảng xoay ngang để có trải nghiệm đầy đủ.
        </div>
    </div>
</div>
@elseif($mode === 'blocked-form')
<div class="block lg:hidden text-center py-12 px-4 bg-dark-900 border border-dark-800 rounded-2xl">
    <div class="w-16 h-16 mx-auto bg-orange-500/10 text-orange-400 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
    <h3 class="text-lg font-bold text-white mb-2">Chức năng phức tạp</h3>
    <p class="text-dark-400 text-sm mb-6 max-w-sm mx-auto leading-relaxed">
        Biểu mẫu này chứa nhiều trường dữ liệu phức tạp hoặc trình soạn thảo nội dung dài. Để tránh sai sót hoặc mất dữ liệu, vui lòng thực hiện trên máy tính.
    </p>
    <button onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-dark-800 text-white text-sm font-semibold rounded-xl hover:bg-dark-700 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Quay lại
    </button>
</div>
@endif
