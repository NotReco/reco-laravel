<x-admin-layout :pageTitle="isset($frame) ? 'Sửa khung avatar' : 'Thêm khung avatar'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">
                {{ isset($frame) ? 'Chỉnh sửa khung avatar' : 'Thêm khung avatar mới' }}
            </h2>
            <p class="text-sm text-dark-400 mt-1">Upload ảnh PNG/WEBP/GIF/SVG có nền trong suốt để làm khung.</p>
        </div>
        <a href="{{ route('admin.avatar-frames.index') }}"
            class="inline-flex py-2 px-4 border border-dark-700 hover:bg-dark-800 text-white rounded-xl text-sm transition-colors">
            Quay lại
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- ── Form ── --}}
        <div class="lg:col-span-2 bg-dark-900 border border-dark-800 rounded-2xl shadow-sm p-6 sm:p-8">
            <form
                action="{{ isset($frame) ? route('admin.avatar-frames.update', $frame) : route('admin.avatar-frames.store') }}"
                method="POST" enctype="multipart/form-data" class="space-y-6" id="frameForm">
                @csrf
                @if (isset($frame))
                    @method('PUT')
                @endif

                {{-- Name --}}
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-dark-300">
                        Tên khung <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                        class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                        value="{{ old('name', $frame->name ?? '') }}" oninput="updateNamePreview(this.value)" required>
                </div>

                {{-- File upload --}}
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-dark-300">
                        {{ isset($frame) ? 'Thay ảnh khung (tùy chọn)' : 'File ảnh khung' }}
                        @if (!isset($frame))
                            <span class="text-red-500">*</span>
                        @endif
                    </label>

                    <div class="border-2 border-dashed border-dark-700 hover:border-sky-500/50 rounded-xl p-6 transition-colors text-center cursor-pointer"
                        onclick="document.getElementById('image').click()">
                        <svg class="w-10 h-10 text-dark-600 mx-auto mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm text-dark-400 mb-1">Nhấp để chọn file hoặc kéo thả vào đây</p>
                        <p class="text-xs text-dark-600">PNG, WEBP, GIF, SVG — khuyến nghị có nền trong suốt, tối đa 2MB
                        </p>
                        <input type="file" id="image" name="image"
                            accept="image/png, image/gif, image/webp, image/jpeg, image/svg+xml" class="hidden"
                            {{ !isset($frame) ? 'required' : '' }} onchange="previewFrame(this)">
                    </div>

                    <p id="selectedFileName" class="text-xs text-sky-400 hidden"></p>
                </div>

                {{-- Active toggle --}}
                <label class="flex items-center gap-3 cursor-pointer group w-fit">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only"
                            {{ old('is_active', $frame->is_active ?? true) ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-dark-700 peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500 transition-colors">
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-dark-200 group-hover:text-white transition-colors">Kích
                            hoạt?</span>
                        <p class="text-xs text-dark-500">Người dùng có thể trang bị khung này</p>
                    </div>
                </label>

                {{-- Submit --}}
                <div class="pt-4 border-t border-dark-800 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ isset($frame) ? 'Cập nhật' : 'Tải lên & Tạo' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Live Preview ── --}}
        <div class="space-y-4">
            <div class="bg-dark-900 border border-dark-800 rounded-2xl p-5 sticky top-20">
                {{-- Large preview --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="relative w-28 h-28 shrink-0">
                        <div
                            class="w-full h-full rounded-full bg-gradient-to-br from-sky-500 to-blue-700 flex items-center justify-center text-4xl font-bold text-white scale-[1.0475]">
                            A
                        </div>
                        <img id="framePreviewLg" src="{{ isset($frame) ? Storage::url($frame->image_path) : '' }}"
                            alt="Frame preview"
                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-opacity {{ !isset($frame) ? 'opacity-0' : '' }}">
                    </div>

                    <p id="namePreviewLg" class="text-sm font-semibold text-white mt-2">
                        {{ isset($frame) ? $frame->name : 'Tên khung' }}
                    </p>
                </div>

                {{-- Small preview --}}
                <div class="mt-5 pt-4 border-t border-dark-800">
                    <p class="text-xs text-dark-500 mb-4 text-center">Preview kích thước nhỏ (nav)</p>
                    <div class="flex items-center justify-center gap-3">
                        <div class="relative w-10 h-10 shrink-0">
                            <div
                                class="w-full h-full rounded-full bg-gradient-to-br from-sky-500 to-blue-700 flex items-center justify-center text-sm font-bold text-white scale-[1.0475]">
                                A</div>
                            <img id="framePreviewSm" src="{{ isset($frame) ? Storage::url($frame->image_path) : '' }}"
                                alt=""
                                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-opacity {{ !isset($frame) ? 'opacity-0' : '' }}">
                        </div>
                        <div>
                            <p class="text-xs font-medium text-white">Người dùng mẫu</p>
                            <p class="text-[10px] text-dark-500">user@example.com</p>
                        </div>
                    </div>
                </div>

                {{-- Stats (edit only) --}}
                @if (isset($frame))
                    <div class="mt-4 pt-4 border-t border-dark-800 grid grid-cols-2 gap-3">
                        <div class="bg-dark-950 rounded-xl p-3 border border-dark-800 text-center">
                            <p class="text-xl font-bold text-white">{{ $frame->users()->count() }}</p>
                            <p class="text-[10px] text-dark-500 mt-0.5">User đang dùng</p>
                        </div>
                        <div class="bg-dark-950 rounded-xl p-3 border border-dark-800 text-center">
                            <p class="text-xs font-bold text-white">{{ $frame->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-dark-500 mt-0.5">Ngày tạo</p>
                        </div>
                    </div>
                @endif

                <p class="text-[10px] text-dark-600 text-center mt-4">
                    * Ảnh khung nên có nền trong suốt (PNG/WEBP/SVG) để hiển thị đúng.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewFrame(input) {
                if (!input.files || !input.files[0]) return;
                const file = input.files[0];

                // Show filename
                const label = document.getElementById('selectedFileName');
                label.textContent = '✓ Đã chọn: ' + file.name;
                label.classList.remove('hidden');

                // Object URL for preview
                const url = URL.createObjectURL(file);

                const lg = document.getElementById('framePreviewLg');
                const sm = document.getElementById('framePreviewSm');

                lg.src = url;
                sm.src = url;
                lg.classList.remove('opacity-0');
                sm.classList.remove('opacity-0');
            }

            function updateNamePreview(val) {
                document.getElementById('namePreviewLg').textContent = val || 'Tên khung';
            }

            // Drag-over styling
            const dropzone = document.querySelector('[onclick]');
            if (dropzone) {
                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('border-sky-500/80', 'bg-sky-500/5');
                });
                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('border-sky-500/80', 'bg-sky-500/5');
                });
                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-sky-500/80', 'bg-sky-500/5');
                    const dt = e.dataTransfer;
                    if (dt.files.length) {
                        const input = document.getElementById('image');
                        input.files = dt.files;
                        previewFrame(input);
                    }
                });
            }
        </script>
    @endpush

</x-admin-layout>
