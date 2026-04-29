<x-admin-layout :pageTitle="$category->exists ? 'Sửa chuyên mục' : 'Thêm chuyên mục'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">
                {{ $category->exists ? 'Chỉnh Sửa Chuyên Mục' : 'Thêm Chuyên Mục Mới' }}
            </h2>
            <p class="text-sm text-dark-400 mt-1">Thông tin quản lý chuyên mục trên diễn đàn.</p>
        </div>
        <a href="{{ route('admin.forum-categories.index') }}"
           class="inline-flex py-2 px-4 border border-dark-700 hover:bg-dark-800 text-white rounded-xl text-sm transition-colors">
            Quay lại
        </a>
    </div>

    <div class="bg-dark-900 border border-dark-800 rounded-2xl shadow-sm p-6 sm:p-8">
        <form action="{{ $category->exists ? route('admin.forum-categories.update', $category) : route('admin.forum-categories.store') }}"
              method="POST"
              class="max-w-2xl space-y-6">
            @csrf
            @if($category->exists)
                @method('PUT')
            @endif

            {{-- Row: Name & Slug --}}
            <div class="grid sm:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-dark-300">
                        Tên chuyên mục <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                           value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="space-y-2">
                    <label for="slug" class="block text-sm font-medium text-dark-300">Đường dẫn (Slug)</label>
                    <input type="text" id="slug" name="slug"
                           class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors"
                           value="{{ old('slug', $category->slug) }}"
                           placeholder="Bỏ trống để tự tạo">
                    <p class="text-xs text-dark-500">Ví dụ: tin-tuc-chung</p>
                </div>
            </div>

            {{-- Description --}}
            <div class="space-y-2">
                <label for="description" class="block text-sm font-medium text-dark-300">Mô tả</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors resize-none">{{ old('description', $category->description) }}</textarea>
            </div>

            {{-- Position & Active --}}
            <div class="grid sm:grid-cols-2 gap-6">

                {{-- ── Position dropdown ── --}}
                <div class="space-y-2">
                    <label for="insert_at" class="block text-sm font-medium text-dark-300">
                        Vị trí <span class="text-red-500">*</span>
                    </label>

                    @php
                        /* Build option list.
                         * For CREATE : existingCategories = all cats, nextOrder = max+1
                         * For EDIT   : existingCategories = all cats EXCEPT current
                         *
                         * insert_at value = the 'order' the new/edited cat will receive.
                         * "Đầu tiên"    → insert_at = 0
                         * "Sau [X]"     → insert_at = X.order + 1
                         * "Cuối danh sách" → insert_at = count (appended after last)
                         */
                        $isEdit = $category->exists;

                        // Total count of OTHER categories (not self)
                        $otherCount = $existingCategories->count();

                        // Determine the currently saved position for the edit form
                        // (so the dropdown pre-selects correctly)
                        $currentOrder = $isEdit ? $category->order : null;
                        $oldInsertAt  = old('insert_at', $currentOrder);
                    @endphp

                    <select id="insert_at" name="insert_at"
                            class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors">

                        {{-- Option: "Đầu tiên" — insert_at = 0 --}}
                        <option value="0" {{ (string)$oldInsertAt === '0' ? 'selected' : '' }}>
                            🔝 Đầu tiên
                        </option>

                        {{-- After each existing category --}}
                        @foreach($existingCategories as $other)
                            @php
                                /*
                                 * For CREATE mode:
                                 *   insert_at = other->order + 1
                                 *   (we push everyone >= that value down)
                                 *
                                 * For EDIT mode:
                                 *   'other' list excludes self, already ordered by DB order.
                                 *   If user picks "After X" (X has order=k among remaining),
                                 *   then the destination order = k + 1 (if k < oldOrder) or k (if k >= oldOrder).
                                 *   BUT to keep it simple we store the final desired order directly.
                                 *
                                 *   Since in edit mode $existingCategories is ordered by 'order'
                                 *   and doesn't include $category itself, we can compute the
                                 *   destination position as: loop index + 1
                                 *   (0-based loop → after slot 0 = position 1, etc.)
                                 */
                                $insertAt = $loop->index + 1;
                            @endphp
                            <option value="{{ $insertAt }}" {{ (string)$oldInsertAt === (string)$insertAt ? 'selected' : '' }}>
                                Sau: {{ $other->name }}
                            </option>
                        @endforeach

                        {{-- Option: "Cuối danh sách" — insert_at = otherCount --}}
                        <option value="{{ $otherCount }}" {{ (string)$oldInsertAt === (string)$otherCount ? 'selected' : '' }}>
                            🔚 Cuối danh sách
                        </option>

                    </select>

                    <p class="text-xs text-dark-500">
                        @if($isEdit)
                            Thứ tự hiện tại: <strong class="text-dark-300">{{ $category->order + 1 }}</strong>.
                            Chọn vị trí mới sẽ đẩy các chuyên mục khác sang để nhường chỗ.
                        @else
                            Chọn vị trí chèn. Các chuyên mục sau sẽ tự động dịch xuống.
                        @endif
                    </p>
                </div>

                {{-- Active toggle --}}
                <div class="flex items-center h-full pt-6">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="is_active" value="1"
                                   class="peer sr-only"
                                   {{ old('is_active', $category->exists ? $category->is_active : true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-dark-700 peer-focus:outline-none rounded-full peer
                                        peer-checked:after:translate-x-full peer-checked:after:border-white
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:border-gray-300 after:border after:rounded-full
                                        after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500 transition-colors"></div>
                        </div>
                        <span class="text-sm font-medium text-dark-200 group-hover:text-white transition-colors">Hiển thị ra ngoài?</span>
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
                    {{ $category->exists ? 'Cập nhật' : 'Tạo mới' }}
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
