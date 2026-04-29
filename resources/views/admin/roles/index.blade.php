<x-superadmin-layout title="Nhóm Quyền" pageTitle="Quản lý Nhóm Quyền">

    <div class="mb-6 flex justify-end">
        <a href="{{ route('super.roles.create') }}" class="btn-primary py-2.5 px-5 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tạo Nhóm Quyền
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-dark-700 text-dark-400 text-left">
                        <th class="px-5 py-3 font-medium">Tên Nhóm Quyền</th>
                        <th class="px-5 py-3 font-medium">Danh sách Quyền</th>
                        <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800">
                    @forelse($roles as $role)
                        <tr class="hover:bg-dark-800/30 transition-colors">
                            <td class="px-5 py-3 font-medium text-white">{{ $role->name }}</td>
                            <td class="px-5 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions as $permission)
                                        <span class="badge text-[10px] bg-sky-500/20 text-sky-400">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('super.roles.edit', $role) }}" class="text-dark-400 hover:text-sky-400 transition-colors p-1" title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('super.roles.destroy', $role) }}" method="POST"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm quyền này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-dark-400 hover:text-red-400 transition-colors p-1" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-dark-500">Chưa có nhóm quyền nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-superadmin-layout>
