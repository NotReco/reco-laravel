<x-superadmin-layout :pageTitle="$quest->exists ? 'Sửa nhiệm vụ' : 'Thêm nhiệm vụ'">

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
                    <textarea id="description" name="description" rows="1"
                              class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors resize-none overflow-hidden"
                              oninput="autoResize(this)">{{ old('description', $quest->description) }}</textarea>
                </div>

                {{-- Divider --}}
                <div class="border-t border-dark-800"></div>

                {{-- Condition --}}
                <div class="space-y-4">
                    <p class="text-sm font-semibold text-dark-200">Điều kiện hoàn thành</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="type" class="block text-xs font-medium text-dark-400">
                                Loại điều kiện <span class="text-red-500">*</span>
                            </label>
                            @php
                                $selectedType = old('type', $quest->type?->value ?? collect($questTypes)->first()?->value);
                            @endphp
                            <div class="relative" x-data="{
                                open: false,
                                selected: @js($selectedType),
                                options: @js(collect($questTypes)->map(fn($t) => ['value' => $t->value, 'label' => $t->label(), 'desc' => $t->description()])->values()),
                                get label() { return this.options.find(o => o.value === this.selected)?.label ?? 'Chọn loại'; }
                            }" @keydown.escape="open=false">
                                {{-- Trigger button --}}
                                <button type="button" @click="open=!open"
                                        class="w-full flex items-center justify-between px-4 py-2.5 bg-dark-800 border border-dark-600 rounded-xl text-dark-100 text-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-colors cursor-pointer"
                                        :class="open ? 'border-sky-500 ring-1 ring-sky-500' : ''">
                                    <span x-text="label"></span>
                                    <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                {{-- Hidden real select for form --}}
                                <select id="type" name="type" class="sr-only" x-model="selected" @change="updateTypeHint(selected)">
                                    @foreach($questTypes as $t)
                                        <option value="{{ $t->value }}" data-desc="{{ $t->description() }}">{{ $t->label() }}</option>
                                    @endforeach
                                </select>
                                {{-- Dropdown panel --}}
                                <div x-show="open" x-cloak @click.outside="open=false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute z-50 w-full mt-1 bg-dark-800 border border-dark-600 rounded-xl shadow-2xl overflow-hidden">
                                    <div class="py-1 max-h-60 overflow-y-auto">
                                        <template x-for="opt in options" :key="opt.value">
                                            <button type="button"
                                                    @click="selected=opt.value; open=false; updateTypeHint(opt.value)"
                                                    class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between gap-2"
                                                    :class="selected===opt.value ? 'bg-sky-500/15 text-sky-300' : 'text-dark-200 hover:bg-dark-700 hover:text-white'">
                                                <span x-text="opt.label"></span>
                                                <svg x-show="selected===opt.value" class="w-3.5 h-3.5 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <p id="typeHint" class="text-xs text-dark-500 min-h-[1.25rem]"></p>
                        </div>

                        <div class="space-y-1.5">
                            <label for="target_value" class="block text-xs font-medium text-dark-400">
                                Ngưỡng cần đạt <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-stretch bg-dark-950 border border-dark-800 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-colors h-[42px]" x-data="{ val: {{ old('target_value', $quest->target_value ?? 1) }} }">
                                <button type="button" @click="val = Math.max(1, parseInt(val) - 1)" class="w-11 flex items-center justify-center text-dark-400 hover:text-white hover:bg-dark-800 border-r border-dark-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <input type="number" id="target_value" name="target_value" min="1" x-model="val"
                                       class="flex-1 bg-transparent text-white px-3 py-2 text-center outline-none w-full border-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-inner-spin-button]:m-0"
                                       required>
                                <button type="button" @click="val = parseInt(val) + 1" class="w-11 flex items-center justify-center text-dark-400 hover:text-white hover:bg-dark-800 border-l border-dark-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <p class="text-xs text-dark-500">Ví dụ: 10 = cần đủ 10 lần</p>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-dark-800"></div>

                {{-- Reward type --}}
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-dark-200">Phần thưởng <span class="text-red-500">*</span></p>

                    {{-- Card-style radio --}}
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="reward_type[]" value="title" class="sr-only"
                                   {{ in_array('title', old('reward_type', $quest->reward_type === 'both' ? ['title', 'frame'] : (array)($quest->reward_type ?? 'title'))) ? 'checked' : '' }}
                                   onchange="toggleReward('title', this.checked)">
                            <div id="reward-card-title" class="flex items-center gap-3 p-3.5 rounded-xl border border-dark-700 bg-dark-950 transition-all">
                                <span class="text-xl">🏷</span>
                                <div>
                                    <p class="text-sm font-semibold text-white">Danh hiệu</p>
                                    <p class="text-xs text-dark-500">Badge tên hiển thị</p>
                                </div>
                                <span id="reward-dot-title" class="ml-auto w-4 h-4 rounded border-2 border-dark-600 flex-shrink-0 transition-all flex items-center justify-center">
                                    <svg id="reward-dot-inner-title" class="w-3 h-3 text-white transition-all scale-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="reward_type[]" value="frame" class="sr-only"
                                   {{ in_array('frame', old('reward_type', $quest->reward_type === 'both' ? ['title', 'frame'] : (array)($quest->reward_type))) ? 'checked' : '' }}
                                   onchange="toggleReward('frame', this.checked)">
                            <div id="reward-card-frame" class="flex items-center gap-3 p-3.5 rounded-xl border border-dark-700 bg-dark-950 transition-all">
                                <span class="text-xl">🖼</span>
                                <div>
                                    <p class="text-sm font-semibold text-white">Khung avatar</p>
                                    <p class="text-xs text-dark-500">Khung ảnh đại diện</p>
                                </div>
                                <span id="reward-dot-frame" class="ml-auto w-4 h-4 rounded border-2 border-dark-600 flex-shrink-0 transition-all flex items-center justify-center">
                                    <svg id="reward-dot-inner-frame" class="w-3 h-3 text-white transition-all scale-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>
                        </label>
                    </div>

                    {{-- Title picker --}}
                    <div id="rewardTitleSection" class="space-y-1.5">
                        <label class="block text-xs font-medium text-dark-400">Chọn danh hiệu</label>
                        @php $selectedTitleId = old('reward_title_id', $quest->reward_title_id); @endphp
                        <div class="relative" x-data="{
                            open: false,
                            selected: @js((string)$selectedTitleId),
                            options: @js($titles->map(fn($t) => ['value' => (string)$t->id, 'label' => $t->name])),
                            get label() { return this.options.find(o => o.value === this.selected)?.label ?? '— Chọn danh hiệu —'; }
                        }" @keydown.escape="open=false">
                            <button type="button" @click="open=!open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 bg-dark-800 border border-dark-600 rounded-xl text-dark-100 text-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-colors cursor-pointer"
                                    :class="open ? 'border-sky-500 ring-1 ring-sky-500' : ''">
                                <span x-text="label" :class="selected ? 'text-dark-100' : 'text-dark-500'"></span>
                                <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <select id="reward_title_id" name="reward_title_id" class="sr-only" x-model="selected">
                                <option value=""></option>
                                @foreach($titles as $title)
                                    <option value="{{ $title->id }}">{{ $title->name }}</option>
                                @endforeach
                            </select>
                            <div x-show="open" x-cloak @click.outside="open=false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-50 w-full mt-1 bg-dark-800 border border-dark-600 rounded-xl shadow-2xl overflow-hidden">
                                <div class="py-1 max-h-60 overflow-y-auto">
                                    <button type="button" @click="selected=''; open=false"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-colors text-dark-500 hover:bg-dark-700 hover:text-white italic">
                                        — Chọn danh hiệu —
                                    </button>
                                    <template x-for="opt in options" :key="opt.value">
                                        <button type="button"
                                                @click="selected=opt.value; open=false"
                                                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between gap-2"
                                                :class="selected===opt.value ? 'bg-sky-500/15 text-sky-300' : 'text-dark-200 hover:bg-dark-700 hover:text-white'">
                                            <span x-text="opt.label"></span>
                                            <svg x-show="selected===opt.value" class="w-3.5 h-3.5 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Frame picker --}}
                    <div id="rewardFrameSection" class="space-y-1.5 hidden">
                        <label class="block text-xs font-medium text-dark-400">Chọn khung avatar</label>
                        @php $selectedFrameId = old('reward_frame_id', $quest->reward_frame_id); @endphp
                        <div class="relative" x-data="{
                            open: false,
                            selected: @js((string)$selectedFrameId),
                            options: @js($frames->map(fn($f) => ['value' => (string)$f->id, 'label' => $f->name])),
                            get label() { return this.options.find(o => o.value === this.selected)?.label ?? '— Chọn khung —'; }
                        }" @keydown.escape="open=false">
                            <button type="button" @click="open=!open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 bg-dark-800 border border-dark-600 rounded-xl text-dark-100 text-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-colors cursor-pointer"
                                    :class="open ? 'border-sky-500 ring-1 ring-sky-500' : ''">
                                <span x-text="label" :class="selected ? 'text-dark-100' : 'text-dark-500'"></span>
                                <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <select id="reward_frame_id" name="reward_frame_id" class="sr-only" x-model="selected">
                                <option value=""></option>
                                @foreach($frames as $frame)
                                    <option value="{{ $frame->id }}">{{ $frame->name }}</option>
                                @endforeach
                            </select>
                            <div x-show="open" x-cloak @click.outside="open=false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-50 w-full mt-1 bg-dark-800 border border-dark-600 rounded-xl shadow-2xl overflow-hidden">
                                <div class="py-1 max-h-60 overflow-y-auto">
                                    <button type="button" @click="selected=''; open=false"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-colors text-dark-500 hover:bg-dark-700 hover:text-white italic">
                                        — Chọn khung —
                                    </button>
                                    <template x-for="opt in options" :key="opt.value">
                                        <button type="button"
                                                @click="selected=opt.value; open=false"
                                                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between gap-2"
                                                :class="selected===opt.value ? 'bg-sky-500/15 text-sky-300' : 'text-dark-200 hover:bg-dark-700 hover:text-white'">
                                            <span x-text="opt.label"></span>
                                            <svg x-show="selected===opt.value" class="w-3.5 h-3.5 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-dark-800"></div>

                {{-- Sort order + Active --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="sort_order" class="block text-sm font-medium text-dark-300">Thứ tự hiển thị</label>
                        <div class="flex items-stretch bg-dark-950 border border-dark-800 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-colors h-[42px]" x-data="{ val: {{ old('sort_order', $quest->sort_order ?? 0) }} }">
                            <button type="button" @click="val = Math.max(0, parseInt(val) - 1)" class="w-11 flex items-center justify-center text-dark-400 hover:text-white hover:bg-dark-800 border-r border-dark-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <input type="number" id="sort_order" name="sort_order" min="0" x-model="val"
                                   class="flex-1 bg-transparent text-white px-3 py-2 text-center outline-none w-full border-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-inner-spin-button]:m-0"
                                   required>
                            <button type="button" @click="val = parseInt(val) + 1" class="w-11 flex items-center justify-center text-dark-400 hover:text-white hover:bg-dark-800 border-l border-dark-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <p class="text-xs text-dark-500">Số nhỏ xuất hiện trước.</p>
                    </div>

                    <div class="flex items-center h-full pt-4">
                        <label class="flex items-center gap-3 cursor-pointer group w-full p-3.5 rounded-xl border border-dark-800 bg-dark-950 hover:border-dark-700 transition-colors">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" name="is_active" value="1" class="peer sr-only"
                                       {{ old('is_active', $quest->is_active ?? true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-dark-700 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                            after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500 transition-colors"></div>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-dark-200 group-hover:text-white transition-colors block">Kích hoạt nhiệm vụ</span>
                                <span class="text-xs text-dark-600">Hiển thị và theo dõi tiến độ</span>
                            </div>
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

        {{-- ── Side panel ── --}}
        <div class="space-y-4">
            <div class="bg-dark-900 border border-dark-800 rounded-2xl p-5 sticky top-20 space-y-4">

                {{-- Steps --}}
                <p class="text-xs font-semibold text-dark-400 uppercase tracking-wider">Hướng dẫn</p>
                <div class="space-y-3">
                    @foreach([
                        ['icon' => '🎯', 'color' => 'sky', 'title' => 'Loại điều kiện', 'desc' => 'Chọn hành động bạn muốn khuyến khích người dùng thực hiện.'],
                        ['icon' => '🔢', 'color' => 'violet', 'title' => 'Ngưỡng cần đạt', 'desc' => 'Số lần cần đạt để hoàn thành, ví dụ 10 = làm đủ 10 lần.'],
                        ['icon' => '🎁', 'color' => 'emerald', 'title' => 'Phần thưởng', 'desc' => 'Chọn danh hiệu hoặc khung avatar đã tạo sẵn.'],
                        ['icon' => '⚡', 'color' => 'amber', 'title' => 'Tự động phát thưởng', 'desc' => 'Hệ thống tự cấp thưởng và thông báo khi user đạt điều kiện.'],
                    ] as $step)
                        <div class="flex gap-3 items-start">
                            <span class="text-base shrink-0 mt-0.5">{{ $step['icon'] }}</span>
                            <div>
                                <p class="text-xs font-semibold text-white">{{ $step['title'] }}</p>
                                <p class="text-xs text-dark-500 leading-relaxed mt-0.5">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
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

    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    function setRewardCardState(type, isChecked) {
        const card = document.getElementById('reward-card-' + type);
        const dot  = document.getElementById('reward-dot-' + type);
        const inner = document.getElementById('reward-dot-inner-' + type);
        if (!card) return;
        if (isChecked) {
            card.classList.add('border-sky-500', 'bg-sky-500/10');
            card.classList.remove('border-dark-700');
            dot.classList.add('border-sky-500', 'bg-sky-500');
            dot.classList.remove('border-dark-600');
            inner.classList.remove('scale-0');
            inner.classList.add('scale-100');
        } else {
            card.classList.remove('border-sky-500', 'bg-sky-500/10');
            card.classList.add('border-dark-700');
            dot.classList.remove('border-sky-500', 'bg-sky-500');
            dot.classList.add('border-dark-600');
            inner.classList.add('scale-0');
            inner.classList.remove('scale-100');
        }
    }

    function toggleReward(type, isChecked) {
        if (type === 'title') {
            document.getElementById('rewardTitleSection').classList.toggle('hidden', !isChecked);
        } else if (type === 'frame') {
            document.getElementById('rewardFrameSection').classList.toggle('hidden', !isChecked);
        }
        setRewardCardState(type, isChecked);
    }

    function updateTypeHint(value) {
        const select = document.getElementById('type');
        const selected = select.querySelector(`option[value="${value}"]`);
        document.getElementById('typeHint').textContent = selected?.dataset.desc ?? '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('input[name="reward_type[]"]');
        checkboxes.forEach(cb => {
            toggleReward(cb.value, cb.checked);
        });

        const typeSelect = document.getElementById('type');
        updateTypeHint(typeSelect.value);

        // Auto-resize textarea on load (for edit mode)
        const desc = document.getElementById('description');
        if (desc && desc.value) autoResize(desc);
    });
    </script>
    @endpush

</x-superadmin-layout>

