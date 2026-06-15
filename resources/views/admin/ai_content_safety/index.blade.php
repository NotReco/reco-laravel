<x-admin-layout>
    <x-slot:title>AI Content Safety Center</x-slot>
    <x-slot:pageTitle>AI Content Safety Center</x-slot>

    <!-- Header Description -->
    <div class="mb-6 bg-dark-900 border border-dark-800 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-2">Trạm kiểm duyệt AI & Báo cáo</h2>
        <p class="text-dark-400 text-sm">
            Nơi tập trung theo dõi nhật ký chặn nội dung của AI/Rule, xử lý các báo cáo vi phạm từ người dùng, 
            và quản lý các nội dung đang bị ẩn hoặc xóa tạm thời.
        </p>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex gap-2 overflow-x-auto pb-4 mb-4 border-b border-dark-800 scrollbar-hide">
        <a href="{{ route('admin.ai-content-safety.index', ['tab' => 'ai_logs']) }}" 
           class="px-5 py-2.5 rounded-full text-sm font-semibold transition-colors whitespace-nowrap
           {{ $tab === 'ai_logs' ? 'bg-sky-600 text-white' : 'bg-dark-800 text-dark-400 hover:text-white' }}">
            Lịch sử chặn (AI & Rule)
        </a>
        <a href="{{ route('admin.ai-content-safety.index', ['tab' => 'reports']) }}" 
           class="px-5 py-2.5 rounded-full text-sm font-semibold transition-colors whitespace-nowrap
           {{ $tab === 'reports' ? 'bg-orange-600 text-white' : 'bg-dark-800 text-dark-400 hover:text-white' }}">
            Chờ xử lý (User Reports)
        </a>
        <a href="{{ route('admin.ai-content-safety.index', ['tab' => 'hidden_content']) }}" 
           class="px-5 py-2.5 rounded-full text-sm font-semibold transition-colors whitespace-nowrap
           {{ $tab === 'hidden_content' ? 'bg-red-600 text-white' : 'bg-dark-800 text-dark-400 hover:text-white' }}">
            Nội dung đã ẩn/xóa (Hidden)
        </a>
    </div>

    <!-- TAB 1: AI LOGS -->
    @if($tab === 'ai_logs' && isset($aiLogs))
        <div class="bg-dark-900 border border-dark-800 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-dark-300">
                    <thead class="bg-dark-800/50 text-xs uppercase text-dark-400">
                        <tr>
                            <th class="px-4 py-3">Thời gian</th>
                            <th class="px-4 py-3">Người dùng</th>
                            <th class="px-4 py-3">Nguồn / Mức độ</th>
                            <th class="px-4 py-3 w-1/2">Nội dung chặn</th>
                            <th class="px-4 py-3">Từ khóa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-800">
                        @forelse($aiLogs as $log)
                            <tr class="hover:bg-dark-800/30">
                                <td class="px-4 py-3 whitespace-nowrap">{{ $log['created_at']->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-white">
                                    {{ $log['user_name'] }}
                                    <div class="text-xs text-dark-500 font-normal">{{ $log['ip_address'] }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1 mb-1">
                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded bg-sky-500/20 text-sky-400 uppercase">
                                            {{ $log['source'] }}
                                        </span>
                                        @if($log['severity'] !== 'null')
                                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded bg-orange-500/20 text-orange-400 uppercase">
                                                {{ $log['severity'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-dark-500">{{ $log['status'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <div class="whitespace-pre-wrap leading-relaxed max-h-32 overflow-y-auto bg-dark-950 p-2 rounded border border-dark-800 text-dark-300 italic">{{ $log['excerpt'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($log['matched_words'])
                                        <span class="text-red-400 font-semibold">{{ $log['matched_words'] }}</span>
                                    @else
                                        <span class="text-dark-600">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-dark-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p>Tuyệt vời! Không có dữ liệu nhật ký chặn gần đây.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($aiLogs->hasPages())
                <div class="p-4 border-t border-dark-800">
                    {{ $aiLogs->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: REPORTS -->
    @if($tab === 'reports' && isset($reports))
        <div class="bg-dark-900 border border-dark-800 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-dark-300">
                    <thead class="bg-dark-800/50 text-xs uppercase text-dark-400">
                        <tr>
                            <th class="px-4 py-3">Ngày báo cáo</th>
                            <th class="px-4 py-3">Người báo cáo</th>
                            <th class="px-4 py-3">Mục tiêu / Nội dung vi phạm</th>
                            <th class="px-4 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-800">
                        @forelse($reports as $item)
                            <tr class="hover:bg-dark-800/30">
                                <td class="px-4 py-3 whitespace-nowrap">{{ $item['created_at']->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-white font-medium">
                                    {{ $item['user_name'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-orange-500/20 text-orange-400 uppercase">
                                            {{ str_replace('_', ' ', $item['target_type']) }}
                                        </span>
                                        <span class="text-xs text-dark-400">Lý do: <strong class="text-white">{{ $item['reason'] }}</strong></span>
                                    </div>
                                    <div class="text-xs italic text-dark-400 bg-dark-950 p-2 rounded line-clamp-3">
                                        {{ $item['excerpt'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <!-- Group 1: Xử lý Report -->
                                    <div class="flex items-center justify-end gap-2 mb-2">
                                        <form action="{{ route('admin.ai-content-safety.resolveReport', $item['id']) }}" method="POST">
                                            @csrf
                                            <button class="px-3 py-1.5 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold rounded-lg transition-colors">
                                                Resolve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.ai-content-safety.dismissReport', $item['id']) }}" method="POST">
                                            @csrf
                                            <button class="px-3 py-1.5 bg-dark-700 hover:bg-dark-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                                Bỏ qua
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Group 2: Xử lý Target -->
                                    @if($item['target_id'])
                                        <div class="flex items-center justify-end gap-2 border-t border-dark-800 pt-2 mt-2">
                                            <span class="text-[10px] text-dark-500 mr-1 uppercase">Target:</span>
                                            <form id="hide-target-{{ $item['id'] }}" action="{{ route('admin.ai-content-safety.hideTarget', ['type' => $item['target_type'], 'id' => $item['target_id']]) }}" method="POST">
                                                @csrf
                                                <button type="button" @click="$dispatch('admin-confirm', {
                                                    title: 'Ẩn nội dung?',
                                                    message: 'Bạn có muốn đổi trạng thái nội dung này thành ẩn (hoặc xóa tạm nếu không có trạng thái)?',
                                                    formId: 'hide-target-{{ $item['id'] }}',
                                                    confirmText: 'Đồng ý Ẩn',
                                                    type: 'warning'
                                                })" class="px-2 py-1 bg-orange-600/20 text-orange-400 hover:bg-orange-600 hover:text-white text-[10px] font-semibold rounded transition-colors border border-orange-500/20">
                                                    Ẩn Target
                                                </button>
                                            </form>
                                            <form id="delete-target-{{ $item['id'] }}" action="{{ route('admin.ai-content-safety.deleteTarget', ['type' => $item['target_type'], 'id' => $item['target_id']]) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="button" @click="$dispatch('admin-confirm', {
                                                    title: 'Xóa tạm nội dung?',
                                                    message: 'Hành động này sẽ đưa nội dung vào thùng rác (Soft Delete).',
                                                    formId: 'delete-target-{{ $item['id'] }}',
                                                    confirmText: 'Đồng ý Xóa',
                                                    type: 'danger'
                                                })" class="px-2 py-1 bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white text-[10px] font-semibold rounded transition-colors border border-red-500/20">
                                                    Xóa Target
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-dark-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p>Tuyệt vời! Không có báo cáo nào đang chờ xử lý.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reports->hasPages())
                <div class="p-4 border-t border-dark-800">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 3: HIDDEN/TRASHED CONTENT -->
    @if($tab === 'hidden_content' && isset($hiddenContent))
        <div class="bg-dark-900 border border-dark-800 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-dark-300">
                    <thead class="bg-dark-800/50 text-xs uppercase text-dark-400">
                        <tr>
                            <th class="px-4 py-3">Ngày ẩn/xóa</th>
                            <th class="px-4 py-3">Phân loại</th>
                            <th class="px-4 py-3">Người đăng</th>
                            <th class="px-4 py-3 w-1/2">Nội dung</th>
                            <th class="px-4 py-3">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-800">
                        @forelse($hiddenContent as $item)
                            <tr class="hover:bg-dark-800/30">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $item['created_at'] ? $item['created_at']->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded bg-dark-700 text-dark-300 border border-dark-600">
                                        {{ $item['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-white">
                                    {{ $item['user_name'] }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <div class="line-clamp-2 bg-dark-950 p-2 rounded text-dark-300 italic">
                                        {{ $item['excerpt'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($item['status'] === 'hidden')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-md bg-orange-500/20 text-orange-400">Hidden</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-md bg-red-500/20 text-red-400">Trashed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-dark-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p>Không có nội dung nào bị ẩn hoặc xóa tạm.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($hiddenContent->hasPages())
                <div class="p-4 border-t border-dark-800">
                    {{ $hiddenContent->links() }}
                </div>
            @endif
        </div>
    @endif

</x-admin-layout>
