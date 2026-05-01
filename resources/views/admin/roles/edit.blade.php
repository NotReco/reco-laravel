<x-superadmin-layout :title="isset($role) ? 'Chỉnh sửa nhóm quyền hạn' : 'Tạo nhóm quyền hạn'" :pageTitle="isset($role) ? 'Chỉnh sửa nhóm quyền hạn' : 'Tạo nhóm quyền hạn'">

    <div class="max-w-3xl">
        <form action="{{ isset($role) ? route('super.roles.update', $role) : route('super.roles.store') }}" method="POST"
            class="card p-6">
            @csrf
            @if (isset($role))
                @method('PUT')
            @endif

            <div class="mb-6">
                <label class="block text-sm font-medium text-dark-300 mb-2">Tên nhóm quyền hạn <span
                        class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                    class="input-dark w-full" placeholder="Ví dụ: Movie Moderator">
                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label class="block text-sm font-medium text-dark-300 mb-3">Phân quyền hạn</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($permissions as $permission)
                        <label
                            class="flex items-center gap-3 p-3 rounded-xl border border-dark-700 bg-dark-800/50 hover:bg-dark-700/50 cursor-pointer transition-colors">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                class="rounded border-dark-600 bg-dark-900 text-sky-500 focus:ring-sky-500/50"
                                {{ isset($role) && $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                            <span class="text-sm text-dark-200">{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('permissions')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-dark-700">
                <a href="{{ route('super.roles.index') }}" class="btn-secondary py-2.5 px-6">Hủy</a>
                <button type="submit" class="btn-primary py-2.5 px-6">
                    {{ isset($role) ? 'Cập nhật' : 'Tạo mới' }}
                </button>
            </div>
        </form>
    </div>

</x-superadmin-layout>
