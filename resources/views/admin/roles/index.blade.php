<x-superadmin-layout title="Nhóm quyền hạn" pageTitle="Quản lý nhóm quyền hạn">

    <div x-data="{
        showDeleteModal: false,
        deleteUrl: '',
        roleName: '',
        confirmDelete(url, name) {
            this.deleteUrl = url;
            this.roleName = name;
            this.showDeleteModal = true;
        }
    }">

        <div class="mb-6 flex justify-end">
            <a href="{{ route('super.roles.create') }}" class="btn-primary py-2.5 px-5 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tạo nhóm quyền hạn
            </a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-dark-700 text-dark-400 text-left">
                            <th class="px-5 py-3 font-medium">Tên nhóm quyền hạn</th>
                            <th class="px-5 py-3 font-medium">Danh sách quyền hạn</th>
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
                                        <button type="button" @click="confirmDelete('{{ route('super.roles.destroy', $role) }}', '{{ $role->name }}')" class="text-dark-400 hover:text-red-400 transition-colors p-1" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-dark-500">Chưa có nhóm quyền hạn nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-dark-950/80 backdrop-blur-sm" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDeleteModal" @click.away="showDeleteModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block px-4 pt-5 pb-4 text-left align-bottom transition-all transform bg-dark-900 border border-dark-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-500/10 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-white" id="modal-title">Xóa nhóm quyền hạn</h3>
                            <div class="mt-2">
                                <p class="text-sm text-dark-400">Bạn có chắc chắn muốn xóa nhóm quyền hạn <span class="font-bold text-white" x-text="roleName"></span>? Hành động này không thể hoàn tác.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <form :action="deleteUrl" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors bg-red-500 border border-transparent rounded-xl shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Xóa ngay
                            </button>
                        </form>
                        <button type="button" @click="showDeleteModal = false" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium transition-colors border shadow-sm text-dark-300 bg-dark-800 border-dark-700 rounded-xl hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Hủy bỏ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-superadmin-layout>
