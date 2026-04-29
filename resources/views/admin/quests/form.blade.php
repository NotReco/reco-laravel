<x-admin-layout :pageTitle="$quest->exists ? 'Sửa nhiệm vụ' : 'Thêm nhiệm vụ'">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">
                {{ $quest->exists ? 'Chỉnh sửa: ' . $quest->name : 'Thêm nhiệm vụ mới' }}
            </h2>
            <p class="text-sm text-dark-400 mt-1">Cấu hình điều kiện và phần thưởng cho nhiệm vụ.</p>
        </div>
        <a href="{{ route('admin.quests.index') }}"
           class="inline-flex py-2 px-4 border border-dark-700 hover:bg-dark-800 text-white rounded-xl text-sm transition-colors">
            Quay lại
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- ── Form ── --}}
        <div class="lg:col-span-2 bg-dark-900 border border-dark-800 rounded-2xl p-6 sm:p-8">
            <form action="{{ $quest->exists ? route('admin.quests.update', $quest) : route('admin.quests.store') }}"
                  method="POST" class="space-y-6">
                @csrf
                @if($quest->exists) @method('PUT') @endif

                {{-- Name --}}
                <div class="space-y-1.5">
                    <label for="name" class="block text-sm font-medium text-dark-300">
                        Tên nhiệm vụ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                           value="{{ old('name', $quest->name) }}"
                           placeholder="Ví dụ: Nhà phê bình tập sự, Thành viên lâu năm..."
                           required>
                </div>

                {{-- Description --}}
                <div class="space-y-1.5">
                    <label for="description" class="block text-sm font-medium text-dark-300">Mô tả</label>
                    <textarea id="description" name="description" rows="2"
                              class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors resize-none"
                              placeholder="Mô tả yêu cầu để hoàn thành nhiệm vụ...">{{ old('description', $quest->description) }}</textarea>
                </div>

                {{-- Condition --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="type" class="block text-sm font-medium text-dark-300">
                            Loại điều kiện <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type"
                                class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                                onchange="updateTypeHint(this.value)">
                            @foreach($questTypes as $type)
                                <option value="{{ $type->value }}"
                                        data-desc="{{ $type->description() }}"
                                        {{ old('type', $quest->type?->value) === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        <p id="typeHint" class="text-xs text-dark-500 min-h-[1.25rem]"></p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="target_value" class="block text-sm font-medium text-dark-300">
                            Ngưỡng cần đạt <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="target_value" name="target_value" min="1"
                               class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                               value="{{ old('target_value', $quest->target_value ?? 1) }}"
                               required>
                        <p class="text-xs text-dark-500">Ví dụ: 10 = cần đủ 10 lần</p>
                    </div>
                </div>

                {{-- Reward type --}}
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-dark-300">
                        Loại phần thưởng <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="reward_type" value="title"
                                   class="text-sky-500 focus:ring-sky-500/50 bg-dark-900 border-dark-600"
                                   {{ old('reward_type', $quest->reward_type ?? 'title') === 'title' ? 'checked' : '' }}
                                   onchange="toggleReward('title')">
                            <span class="text-sm text-white">🏷 Danh hiệu</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="reward_type" value="frame"
                                   class="text-sky-500 focus:ring-sky-500/50 bg-dark-900 border-dark-600"
                                   {{ old('reward_type', $quest->reward_type) === 'frame' ? 'checked' : '' }}
                                   onchange="toggleReward('frame')">
                            <span class="text-sm text-white">🖼 Khung avatar</span>
                        </label>
                    </div>

                    {{-- Title picker --}}
                    <div id="rewardTitleSection" class="space-y-1.5">
                        <label for="reward_title_id" class="block text-xs font-medium text-dark-400">Chọn danh hiệu</label>
                        <select id="reward_title_id" name="reward_title_id"
                                class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors">
                            <option value="">— Chọn danh hiệu —</option>
                            @foreach($titles as $title)
                                <option value="{{ $title->id }}"
                                        data-color="{{ $title->color_hex }}"
                                        {{ old('reward_title_id', $quest->reward_title_id) == $title->id ? 'selected' : '' }}>
                                    {{ $title->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Frame picker --}}
                    <div id="rewardFrameSection" class="space-y-1.5 hidden">
                        <label for="reward_frame_id" class="block text-xs font-medium text-dark-400">Chọn khung avatar</label>
                        <select id="reward_frame_id" name="reward_frame_id"
                                class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors">
                            <option value="">— Chọn khung —</option>
                            @foreach($frames as $frame)
                                <option value="{{ $frame->id }}"
                                        {{ old('reward_frame_id', $quest->reward_frame_id) == $frame->id ? 'selected' : '' }}>
                                    {{ $frame->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Sort order + Active --}}
                <div class="grid sm:grid-cols-2 gap-4 pt-2">
                    <div class="space-y-1.5">
                        <label for="sort_order" class="block text-sm font-medium text-dark-300">Thứ tự hiển thị</label>
                        <input type="number" id="sort_order" name="sort_order" min="0"
                               class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                               value="{{ old('sort_order', $quest->sort_order ?? 0) }}">
                        <p class="text-xs text-dark-500">Số nhỏ xuất hiện trước.</p>
                    </div>

                    <div class="flex items-center h-full pt-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" name="is_active" value="1" class="peer sr-only"
                                       {{ old('is_active', $quest->is_active ?? true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-dark-700 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                            after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500 transition-colors"></div>
                            </div>
                            <span class="text-sm font-medium text-dark-200 group-hover:text-white transition-colors">Kích hoạt nhiệm vụ</span>
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-4 border-t border-dark-800 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $quest->exists ? 'Cập nhật' : 'Tạo nhiệm vụ' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Help panel ── --}}
        <div class="space-y-4">
            <div class="bg-dark-900 border border-dark-800 rounded-2xl p-5 sticky top-20 space-y-5">
                <div>
                    <p class="text-xs font-semibold text-dark-400 uppercase tracking-wider mb-3">Hướng dẫn</p>
                    <ul class="text-xs text-dark-400 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-sky-400 shrink-0 mt-0.5">1.</span>
                            Chọn <strong class="text-white">loại điều kiện</strong> phù hợp với hành động bạn muốn khuyến khích.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-400 shrink-0 mt-0.5">2.</span>
                            Đặt <strong class="text-white">ngưỡng</strong> — ví dụ 10 nghĩa là user phải làm đủ 10 lần.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-400 shrink-0 mt-0.5">3.</span>
                            Chọn <strong class="text-white">phần thưởng</strong> từ danh hiệu hoặc khung đã tạo sẵn.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-400 shrink-0 mt-0.5">4.</span>
                            Hệ thống sẽ <strong class="text-white">tự động phát thưởng</strong> và gửi thông báo khi user đạt điều kiện.
                        </li>
                    </ul>
                </div>

                {{-- Stats (edit only) --}}
                @if($quest->exists)
                <div class="pt-4 border-t border-dark-800">
                    <p class="text-xs font-semibold text-dark-400 uppercase tracking-wider mb-3">Thống kê</p>
                    @php
                        $completedCount = $quest->userProgress()->whereNotNull('completed_at')->count();
                        $inProgressCount = $quest->userProgress()->whereNull('completed_at')->count();
                    @endphp
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-dark-950 rounded-xl p-3 border border-dark-800 text-center">
                            <p class="text-xl font-bold text-emerald-400">{{ $completedCount }}</p>
                            <p class="text-[10px] text-dark-500 mt-0.5">Đã hoàn thành</p>
                        </div>
                        <div class="bg-dark-950 rounded-xl p-3 border border-dark-800 text-center">
                            <p class="text-xl font-bold text-sky-400">{{ $inProgressCount }}</p>
                            <p class="text-[10px] text-dark-500 mt-0.5">Đang thực hiện</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function toggleReward(type) {
        document.getElementById('rewardTitleSection').classList.toggle('hidden', type !== 'title');
        document.getElementById('rewardFrameSection').classList.toggle('hidden', type !== 'frame');
    }

    function updateTypeHint(value) {
        const select = document.getElementById('type');
        const selected = select.querySelector(`option[value="${value}"]`);
        document.getElementById('typeHint').textContent = selected?.dataset.desc ?? '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const checkedRadio = document.querySelector('input[name="reward_type"]:checked');
        if (checkedRadio) toggleReward(checkedRadio.value);

        const typeSelect = document.getElementById('type');
        updateTypeHint(typeSelect.value);
    });
    </script>
    @endpush

</x-admin-layout>
