<x-admin-layout pageTitle="Thêm Khung Avatar">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">Thêm Khung Mới</h2>
        </div>
        <a href="{{ route('admin.avatar-frames.index') }}" class="inline-flex py-2 px-4 border border-dark-700 hover:bg-dark-800 text-white rounded-xl text-sm transition-colors">
            Quay lại
        </a>
    </div>

    <div class="bg-dark-900 border border-dark-800 rounded-2xl shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.avatar-frames.store') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-dark-300">Tên khung avatar <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" 
                       class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors" 
                       value="{{ old('name') }}" required>
            </div>

            <div class="space-y-2">
                <label for="image" class="block text-sm font-medium text-dark-300">File Ảnh Nền Trong (PNG/GIF/WEBP/SVG) <span class="text-red-500">*</span></label>
                <input type="file" id="image" name="image" accept="image/png, image/gif, image/webp, image/jpeg, image/svg+xml"
                       class="w-full text-sm text-dark-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-dark-800 file:text-white hover:file:bg-dark-700 transition" required>
            </div>

            <div class="flex items-center h-full">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only" checked>
                        <div class="w-11 h-6 bg-dark-700 peer-focus:outline-none rounded-full peer 
                                    peer-checked:after:translate-x-full peer-checked:after:border-white 
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                    after:bg-white after:border-gray-300 after:border after:rounded-full 
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500 transition-colors"></div>
                    </div>
                    <span class="text-sm font-medium text-dark-200 group-hover:text-white transition-colors">Kích hoạt?</span>
                </label>
            </div>

            <div class="pt-4 border-t border-dark-800 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
                    Tải lên & Tạo
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
