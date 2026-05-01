<x-superadmin-layout title="Nhân viên" pageTitle="Quản lý nhân viên nội bộ">
    <div x-data="{
        showConfirmModal: false,
        confirmUrl: '',
        confirmAction: '',
        accountName: '',
        confirm(url, action, name) {
            this.confirmUrl = url;
            this.confirmAction = action;
            this.accountName = name;
            this.showConfirmModal = true;
        }
    }">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-white">Danh sách nhân viên nội bộ</h2>
                <p class="text-sm text-dark-400 mt-1">
                    Quản lý tài khoản nội bộ — chỉ Super Admin mới truy cập được trang này.
                </p>
            </div>
            <a href="{{ route('super.staff.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-xl hover:bg-indigo-600 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm tài khoản
            </a>
        </div>

        {{-- Warning banner --}}
        <div
            class="mb-5 flex items-start gap-3 p-4 rounded-xl border border-amber-500/30 bg-amber-500/10 text-amber-300 text-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p class="font-semibold text-amber-200">Khu vực nhạy cảm</p>
                <p class="text-amber-300/80 mt-0.5">Tài khoản kiểm duyệt viên (Mod) và quản trị viên (Admin) có quyền
                    truy cập Control Panel. Mọi thay đổi
                    tại đây có ảnh hưởng đến bảo mật hệ thống.</p>
            </div>
        </div>

        {{-- Filters --}}
        <form action="{{ route('super.staff.index') }}" method="GET" class="flex gap-3 mb-6 max-w-xl">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên hoặc email..."
                class="input-dark text-sm flex-1 py-2.5" autocomplete="off">
            <select name="role" class="input-dark text-sm w-44 py-2.5">
                <option value="">Tất cả</option>
                <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Kiểm duyệt viên
                </option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên</option>
            </select>
            <button type="submit" class="btn-secondary py-2.5 px-5 text-sm">Lọc</button>
        </form>

        {{-- Table --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-dark-700 text-dark-400 text-left">
                            <th class="px-5 py-3 font-medium">Tài khoản</th>
                            <th class="px-5 py-3 font-medium">Vai trò</th>
                            <th class="px-5 py-3 font-medium">Nhóm quyền</th>
                            <th class="px-5 py-3 font-medium">Trạng thái</th>
                            <th class="px-5 py-3 font-medium">Đăng nhập gần nhất</th>
                            <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-800">
                        @forelse($staffAccounts as $account)
                            <tr
                                class="hover:bg-dark-800/30 transition-colors {{ $account->id === auth()->id() ? 'bg-indigo-500/5' : '' }}">

                                {{-- Avatar + info --}}
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                                            @if ($account->avatar)
                                                <img src="{{ $account->avatar }}" class="w-full h-full object-cover"
                                                    alt="">
                                            @else
                                                <span
                                                    class="text-xs font-bold text-white">{{ strtoupper(substr($account->name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-white truncate">
                                                {{ $account->name }}
                                                @if ($account->id === auth()->id())
                                                    <span
                                                        class="text-[10px] text-indigo-400 font-normal ml-1">(bạn)</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-dark-500 truncate">{{ $account->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td class="px-5 py-3">
                                    <span
                                        class="badge text-[10px] {{ match ($account->role->color()) {
                                            'gray' => 'bg-gray-500/20 text-gray-400',
                                            'blue' => 'bg-blue-500/20 text-blue-400',
                                            'amber' => 'bg-amber-500/20 text-amber-400',
                                            'red' => 'bg-red-500/20 text-red-400',
                                            default => 'bg-gray-500/20 text-gray-400',
                                        } }}">
                                        {{ $account->role->label() }}
                                    </span>
                                </td>

                                {{-- Spatie roles --}}
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($account->roles as $spatieRole)
                                            <span
                                                class="text-[10px] px-1.5 py-0.5 rounded bg-dark-800 border border-dark-700 text-dark-300">
                                                {{ $spatieRole->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-dark-600 italic">—</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-3">
                                    @if ($account->is_active)
                                        <span class="badge text-[10px] bg-emerald-500/20 text-emerald-400">Hoạt
                                            động</span>
                                    @else
                                        <span class="badge text-[10px] bg-red-500/20 text-red-400">Đã khóa</span>
                                    @endif
                                </td>

                                {{-- Last login --}}
                                <td class="px-5 py-3 text-dark-500 text-xs">
                                    {{ $account->last_login_at ? $account->last_login_at->diffForHumans() : 'Chưa đăng nhập' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- Edit --}}
                                        @if ($account->id !== auth()->id())
                                            <a href="{{ route('super.staff.edit', $account) }}"
                                                class="text-dark-400 hover:text-indigo-400 transition-colors p-1.5 rounded-lg hover:bg-indigo-500/10"
                                                title="Chỉnh sửa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- Toggle ban --}}
                                            <button type="button"
                                                @click="confirm('{{ route('super.staff.toggleBan', $account) }}', '{{ $account->is_active ? 'Khóa' : 'Mở khóa' }}', '{{ $account->name }}')"
                                                class="text-dark-400 hover:text-{{ $account->is_active ? 'red' : 'emerald' }}-400 transition-colors p-1.5 rounded-lg hover:bg-{{ $account->is_active ? 'red' : 'emerald' }}-500/10"
                                                title="{{ $account->is_active ? 'Khóa' : 'Mở khóa' }}">
                                                @if ($account->is_active)
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
                                        @else
                                            <span class="text-xs text-dark-600 italic px-2">Tài khoản của bạn</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-dark-500">Chưa có tài khoản nhân
                                    viên
                                    nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $staffAccounts->links() }}
        </div>

        {{-- Confirmation Modal --}}
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-dark-950/80 backdrop-blur-sm" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showConfirmModal" @click.away="showConfirmModal = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block px-4 pt-5 pb-4 text-left align-bottom transition-all transform bg-dark-900 border border-dark-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto rounded-full sm:mx-0 sm:h-10 sm:w-10"
                            :class="confirmAction === 'Khóa' ? 'bg-red-500/10' : 'bg-emerald-500/10'">
                            <svg class="w-6 h-6"
                                :class="confirmAction === 'Khóa' ? 'text-red-500' : 'text-emerald-500'"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-white" id="modal-title"
                                x-text="confirmAction + ' tài khoản'"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-dark-400">Bạn có chắc chắn muốn <span
                                        x-text="confirmAction.toLowerCase()"></span> tài khoản <span
                                        class="font-bold text-white" x-text="'«' + accountName + '»'"></span>?</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <form :action="confirmUrl" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors border border-transparent rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                                :class="confirmAction === 'Khóa' ? 'bg-red-500 hover:bg-red-600 focus:ring-red-500' :
                                    'bg-emerald-500 hover:bg-emerald-600 focus:ring-emerald-500'"
                                x-text="'Xác nhận ' + confirmAction.toLowerCase()">
                            </button>
                        </form>
                        <button type="button" @click="showConfirmModal = false"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium transition-colors border shadow-sm text-dark-300 bg-dark-800 border-dark-700 rounded-xl hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Hủy bỏ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-superadmin-layout>
