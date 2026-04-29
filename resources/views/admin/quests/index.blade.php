<x-admin-layout title="Nhiệm vụ" pageTitle="Quản lý nhiệm vụ">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">Danh sách nhiệm vụ</h2>
            <p class="text-sm text-dark-400 mt-1">Người dùng hoàn thành nhiệm vụ để nhận khung avatar hoặc danh hiệu.</p>
        </div>
        <a href="{{ route('admin.quests.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm nhiệm vụ
        </a>
    </div>

    <div class="bg-dark-900 border border-dark-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-dark-800/50 text-dark-300">
                    <tr>
                        <th class="px-5 py-3 font-medium text-left">#</th>
                        <th class="px-5 py-3 font-medium text-left">Nhiệm vụ</th>
                        <th class="px-5 py-3 font-medium text-left">Điều kiện</th>
                        <th class="px-5 py-3 font-medium text-left">Phần thưởng</th>
                        <th class="px-5 py-3 font-medium text-center">Người hoàn thành</th>
                        <th class="px-5 py-3 font-medium text-center">Trạng thái</th>
                        <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800">
                    @forelse($quests as $quest)
                        <tr class="hover:bg-dark-800/30 transition-colors">

                            {{-- Sort order --}}
                            <td class="px-5 py-3">
                                <span class="text-xs text-dark-600 font-mono">{{ $quest->sort_order }}</span>
                            </td>

                            {{-- Quest name + description --}}
                            <td class="px-5 py-3">
                                <p class="font-semibold text-white">{{ $quest->name }}</p>
                                @if($quest->description)
                                    <p class="text-xs text-dark-500 mt-0.5 max-w-xs truncate">{{ $quest->description }}</p>
                                @endif
                            </td>

                            {{-- Condition --}}
                            <td class="px-5 py-3">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs text-dark-400">{{ $quest->type->label() }}</span>
                                    <span class="text-sm font-semibold text-white">≥ {{ number_format($quest->target_value) }}</span>
                                </div>
                            </td>

                            {{-- Reward --}}
                            <td class="px-5 py-3">
                                @if($quest->reward_type === 'title' && $quest->rewardTitle)
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full shrink-0"
                                             style="background-color: {{ $quest->rewardTitle->color_hex }}"></div>
                                        <span class="text-sm font-bold"
                                              style="color: {{ $quest->rewardTitle->color_hex }}">
                                            {{ $quest->rewardTitle->name }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-dark-600">Danh hiệu</span>
                                @elseif($quest->reward_type === 'frame' && $quest->rewardFrame)
                                    <div class="flex items-center gap-2">
                                        <div class="relative w-8 h-8 shrink-0">
                                            <div class="absolute inset-0 rounded-full bg-gradient-to-br from-sky-600 to-blue-800"></div>
                                            <img src="{{ Storage::url($quest->rewardFrame->image_path) }}"
                                                 class="absolute inset-0 w-full h-full object-contain" alt="">
                                        </div>
                                        <span class="text-sm text-white">{{ $quest->rewardFrame->name }}</span>
                                    </div>
                                    <span class="text-[10px] text-dark-600">Khung avatar</span>
                                @else
                                    <span class="text-xs text-dark-600 italic">—</span>
                                @endif
                            </td>

                            {{-- Completion count --}}
                            <td class="px-5 py-3 text-center">
                                <span class="text-sm text-dark-300">
                                    {{ $quest->user_progress_count }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-3 text-center">
                                @if($quest->is_active)
                                    <span class="badge text-[10px] bg-emerald-500/20 text-emerald-400">Đang hoạt động</span>
                                @else
                                    <span class="badge text-[10px] bg-dark-700 text-dark-400">Tạm ẩn</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.quests.edit', $quest) }}"
                                       class="p-2 text-dark-400 hover:text-sky-400 hover:bg-sky-400/10 rounded-lg transition-colors" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.quests.destroy', $quest) }}" method="POST"
                                          onsubmit="return confirm('Xóa nhiệm vụ «{{ $quest->name }}»?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-dark-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-dark-800 mb-4">
                                    <svg class="w-7 h-7 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-white mb-1">Chưa có nhiệm vụ nào</h3>
                                <p class="text-sm text-dark-500 mb-4">Thêm nhiệm vụ để khuyến khích người dùng hoạt động.</p>
                                <a href="{{ route('admin.quests.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all">
                                    Thêm nhiệm vụ đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-admin-layout>
