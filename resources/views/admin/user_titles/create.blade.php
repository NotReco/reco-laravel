<x-admin-layout :pageTitle="isset($userTitle) ? 'Sửa danh hiệu' : 'Thêm danh hiệu'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">
                {{ isset($userTitle) ? 'Chỉnh sửa danh hiệu' : 'Thêm danh hiệu mới' }}
            </h2>
            <p class="text-sm text-dark-400 mt-1">Cấu hình tên, màu sắc và trạng thái hiển thị.</p>
        </div>
        <a href="{{ route('admin.user-titles.index') }}"
           class="inline-flex py-2 px-4 border border-dark-700 hover:bg-dark-800 text-white rounded-xl text-sm transition-colors">
            Quay lại
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- ── Form ── --}}
        <div class="lg:col-span-2 bg-dark-900 border border-dark-800 rounded-2xl shadow-sm p-6 sm:p-8">
            <form action="{{ isset($userTitle) ? route('admin.user-titles.update', $userTitle) : route('admin.user-titles.store') }}"
                  method="POST" class="space-y-6" id="titleForm">
                @csrf
                @if(isset($userTitle)) @method('PUT') @endif

                {{-- Name --}}
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-dark-300">
                        Tên danh hiệu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                           value="{{ old('name', $userTitle->name ?? '') }}"
                           placeholder="Ví dụ: Nhà phê bình, Cinephile..."
                           oninput="updatePreview()" required>
                </div>

                {{-- Color --}}
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-dark-300">
                        Màu sắc <span class="text-red-500">*</span>
                    </label>

                    {{-- Preset swatches --}}
                    <div class="flex flex-wrap gap-2">
                        @php
                            $presets = [
                                '#38bdf8' => 'Sky',
                                '#a78bfa' => 'Violet',
                                '#f472b6' => 'Pink',
                                '#fb923c' => 'Orange',
                                '#34d399' => 'Emerald',
                                '#facc15' => 'Yellow',
                                '#f87171' => 'Red',
                                '#94a3b8' => 'Slate',
                                '#c084fc' => 'Purple',
                                '#2dd4bf' => 'Teal',
                                '#e879f9' => 'Fuchsia',
                                '#60a5fa' => 'Blue',
                            ];
                        @endphp
                        @foreach($presets as $hex => $label)
                            <button type="button"
                                    onclick="setColor('{{ $hex }}')"
                                    title="{{ $label }}"
                                    class="w-7 h-7 rounded-full border-2 border-transparent hover:border-white/50 transition-all hover:scale-110"
                                    style="background-color: {{ $hex }}">
                            </button>
                        @endforeach
                    </div>

                    <div class="flex gap-3 items-center">
                        <input type="color" id="color_picker"
                               class="h-10 w-10 rounded-lg cursor-pointer border border-dark-700 p-0.5 bg-dark-950"
                               value="{{ old('color_hex', $userTitle->color_hex ?? '#38bdf8') }}"
                               oninput="syncColorFromPicker(this.value)">
                        <input type="text" id="color_hex" name="color_hex"
                               class="flex-1 bg-dark-950 border border-dark-800 rounded-xl text-white font-mono placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                               value="{{ old('color_hex', $userTitle->color_hex ?? '#38bdf8') }}"
                               placeholder="#38bdf8"
                               oninput="syncColorFromText(this.value)"
                               required>
                    </div>
                </div>

                {{-- Description --}}
                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium text-dark-300">Mô tả</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors resize-none"
                              placeholder="Mô tả điều kiện để nhận danh hiệu này..."
                              oninput="updatePreview()">{{ old('description', $userTitle->description ?? '') }}</textarea>
                </div>

                {{-- Active toggle --}}
                <label class="flex items-center gap-3 cursor-pointer group w-fit">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only"
                               {{ old('is_active', $userTitle->is_active ?? true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-dark-700 peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500 transition-colors"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-dark-200 group-hover:text-white transition-colors">Hiển thị ra ngoài?</span>
                        <p class="text-xs text-dark-500">Người dùng có thể thấy và nhận danh hiệu này</p>
                    </div>
                </label>

                {{-- Submit --}}
                <div class="pt-4 border-t border-dark-800 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ isset($userTitle) ? 'Cập nhật' : 'Tạo mới' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Live Preview ── --}}
        <div class="space-y-4">
            <div class="bg-dark-900 border border-dark-800 rounded-2xl p-5 sticky top-20">
                <p class="text-xs font-semibold text-dark-400 uppercase tracking-wider mb-4">Xem trước</p>

                {{-- Profile card preview --}}
                <div class="bg-dark-950 rounded-xl p-4 border border-dark-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-sky-500 to-blue-700 flex items-center justify-center text-lg font-bold text-white shrink-0">
                            A
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Người dùng mẫu</p>
                            {{-- Badge preview --}}
                            <span id="badgePreview"
                                  class="text-xs font-bold px-2 py-0.5 rounded-full inline-block mt-1 transition-all">
                                Danh hiệu
                            </span>
                        </div>
                    </div>
                    <div class="h-px bg-dark-800 mb-3"></div>
                    <p class="text-xs text-dark-500 leading-relaxed" id="descPreview">
                        Mô tả danh hiệu sẽ xuất hiện ở đây...
                    </p>
                </div>

                {{-- Color info --}}
                <div class="mt-4 p-3 bg-dark-950 rounded-xl border border-dark-800">
                    <p class="text-xs text-dark-500 mb-2 font-medium">Màu hiển thị</p>
                    <div class="flex items-center gap-3">
                        <div id="colorSwatch" class="w-8 h-8 rounded-lg border border-dark-700 transition-all"
                             style="background-color: {{ old('color_hex', $userTitle->color_hex ?? '#38bdf8') }}"></div>
                        <div>
                            <p class="text-xs font-mono text-white" id="colorHexDisplay">{{ old('color_hex', $userTitle->color_hex ?? '#38bdf8') }}</p>
                            <p class="text-[10px] text-dark-500">Hex color</p>
                        </div>
                    </div>
                </div>

                {{-- Stats (edit only) --}}
                @if(isset($userTitle))
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="bg-dark-950 rounded-xl p-3 border border-dark-800 text-center">
                            <p class="text-xl font-bold text-white">{{ $userTitle->users()->count() }}</p>
                            <p class="text-[10px] text-dark-500 mt-0.5">User đang dùng</p>
                        </div>
                        <div class="bg-dark-950 rounded-xl p-3 border border-dark-800 text-center">
                            <p class="text-xs font-bold text-white">{{ $userTitle->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-dark-500 mt-0.5">Ngày tạo</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function updatePreview() {
        const name  = document.getElementById('name').value || 'Danh hiệu';
        const desc  = document.getElementById('description').value || 'Mô tả danh hiệu sẽ xuất hiện ở đây...';
        const color = document.getElementById('color_hex').value || '#38bdf8';

        const badge = document.getElementById('badgePreview');
        badge.textContent = name;
        badge.style.color = color;
        badge.style.backgroundColor = color + '22';
        badge.style.border = '1px solid ' + color + '44';

        document.getElementById('descPreview').textContent = desc;
    }

    function syncColorFromPicker(val) {
        document.getElementById('color_hex').value = val;
        syncColor(val);
    }

    function syncColorFromText(val) {
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            document.getElementById('color_picker').value = val;
            syncColor(val);
        }
    }

    function syncColor(val) {
        document.getElementById('colorSwatch').style.backgroundColor = val;
        document.getElementById('colorHexDisplay').textContent = val;
        updatePreview();
    }

    function setColor(hex) {
        document.getElementById('color_hex').value = hex;
        document.getElementById('color_picker').value = hex;
        syncColor(hex);
    }

    // Init preview on load
    document.addEventListener('DOMContentLoaded', updatePreview);
    </script>
    @endpush

</x-admin-layout>
