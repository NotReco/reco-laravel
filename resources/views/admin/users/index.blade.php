<x-admin-layout title="Người dùng" pageTitle="Quản lý thành viên">

    {{-- ── Filters ───────────────────────────────────────────────── --}}
    <div class="mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-3 max-w-2xl">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên hoặc email..."
                class="input-dark text-sm flex-1 py-2.5">
            <select name="role" class="input-dark text-sm w-44 py-2.5">
                <option value="">Tất cả</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Người dùng</option>
                <option value="tester" {{ request('role') === 'tester' ? 'selected' : '' }}>Tester</option>
            </select>
            <button type="submit" class="btn-secondary py-2.5 px-5 text-sm">Lọc</button>
        </form>
    </div>

    {{-- ── Table ─────────────────────────────────────────────────── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-dark-700 text-dark-400 text-left">
                        <th class="px-5 py-3 font-medium">User</th>
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium">Reviews</th>
                        <th class="px-5 py-3 font-medium">Trạng thái</th>
                        <th class="px-5 py-3 font-medium">Ngày tạo</th>
                        <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-dark-800/30 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                                        @if ($user->avatar)
                                            <img src="{{ $user->avatar }}" alt=""
                                                class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <span
                                                class="text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-white truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-dark-500 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="badge text-[10px] bg-{{ $user->role->color() }}-500/20 text-{{ $user->role->color() }}-400">
                                    {{ $user->role->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-dark-400">{{ $user->reviews_count }}</td>
                            <td class="px-5 py-3">
                                @if ($user->is_active)
                                    <span class="badge text-[10px] bg-emerald-500/20 text-emerald-400">Hoạt động</span>
                                @else
                                    <span class="badge text-[10px] bg-red-500/20 text-red-400">Đã khóa</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-dark-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="text-dark-400 hover:text-sky-400 transition-colors p-1"
                                        title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggleBan', $user) }}" method="POST"
                                            onsubmit="return confirm('{{ $user->is_active ? 'Khóa' : 'Mở khóa' }} tài khoản «{{ $user->name }}»?')">
                                            @csrf
                                            <button type="submit"
                                                class="text-dark-400 hover:text-{{ $user->is_active ? 'red' : 'emerald' }}-400 transition-colors p-1"
                                                title="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa' }}">
                                                @if ($user->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-dark-500">Không tìm thấy user nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>

</x-admin-layout>
