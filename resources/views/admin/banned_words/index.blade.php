<x-admin-layout title="Quản lý từ cấm (Moderation)" pageTitle="Quản lý từ cấm (Moderation)">

    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-sky-500/10 border border-sky-500/25">
        <svg class="w-5 h-5 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm text-sky-300">
            Rule-based Moderation (5A). Hệ thống sẽ tự động chặn hoặc ẩn nội dung chứa các từ khóa đang được <span class="font-bold text-sky-200">Kích hoạt</span>.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- ── Form Thêm Mới ──────────────────────────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="card p-5 sticky top-6">
                <h3 class="font-bold text-white mb-4 border-b border-dark-700 pb-3">Thêm từ cấm mới</h3>
                <form action="{{ route('admin.banned_words.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs text-dark-300 mb-1.5 font-medium">Từ khóa (Word)</label>
                        <input type="text" name="word" value="{{ old('word') }}" required placeholder="VD: spam, scam..." class="input-dark w-full text-sm">
                        @error('word')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs text-dark-300 mb-1.5 font-medium">Mức độ (Severity)</label>
                        <select name="severity" class="input-dark w-full text-sm">
                            <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-dark-300 mb-1.5 font-medium">Hành động xử lý (Action)</label>
                        <select name="action" class="input-dark w-full text-sm">
                            <option value="pending" {{ old('action') === 'pending' ? 'selected' : '' }}>Pending (Chặn submit)</option>
                            <option value="hide" {{ old('action') === 'hide' ? 'selected' : '' }}>Hide (Lưu ẩn)</option>
                            <option value="delete" {{ old('action') === 'delete' ? 'selected' : '' }}>Delete (Chặn submit & Báo lỗi)</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-dark-600 bg-dark-800 text-sky-500 w-4 h-4" id="is_active_new">
                        <label for="is_active_new" class="text-sm text-dark-200 cursor-pointer">Kích hoạt ngay</label>
                    </div>

                    <button type="submit" class="btn-primary w-full py-2.5 mt-2">Thêm từ khóa</button>
                </form>
            </div>
        </div>

        {{-- ── Table Danh sách ───────────────────────────────────────────── --}}
        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-dark-700 text-dark-400 text-left">
                                <th class="px-5 py-3 font-medium">Từ khóa</th>
                                <th class="px-5 py-3 font-medium">Mức độ</th>
                                <th class="px-5 py-3 font-medium">Hành động</th>
                                <th class="px-5 py-3 font-medium text-center">Trạng thái</th>
                                <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-800">
                            @forelse($words as $word)
                                <tr class="hover:bg-dark-800/30 transition-colors">
                                    <td class="px-5 py-3">
                                        <span class="font-bold text-white">{{ $word->word }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="badge text-[10px] 
                                            @if($word->severity === 'high') bg-red-500/20 text-red-400
                                            @elseif($word->severity === 'medium') bg-amber-500/20 text-amber-400
                                            @else bg-sky-500/20 text-sky-400 @endif
                                        ">
                                            {{ strtoupper($word->severity) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-dark-300">
                                        <span class="badge text-[10px] border border-dark-600">
                                            {{ strtoupper($word->action) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <form action="{{ route('admin.banned_words.toggle', $word) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium px-2 py-1 rounded {{ $word->is_active ? 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30' : 'bg-dark-700 text-dark-400 hover:bg-dark-600' }}">
                                                {{ $word->is_active ? 'Đang bật' : 'Đang tắt' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <form id="delete-word-{{ $word->id }}" action="{{ route('admin.banned_words.destroy', $word) }}" method="POST" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="button" title="Xóa từ cấm"
                                                class="text-dark-400 hover:text-red-400 transition-colors p-1.5 rounded-lg hover:bg-red-500/10"
                                                @click="$dispatch('admin-confirm', {
                                                    title: 'Xóa từ khóa?',
                                                    message: 'Bạn có chắc chắn muốn xóa từ khóa này khỏi danh sách cấm?',
                                                    formId: 'delete-word-{{ $word->id }}',
                                                    confirmText: 'Xóa',
                                                    type: 'danger'
                                                })">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-dark-500">Chưa có từ khóa nào được thiết lập.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($words->hasPages())
                <div class="mt-6">
                    {{ $words->links() }}
                </div>
            @endif
        </div>
    </div>

</x-admin-layout>
