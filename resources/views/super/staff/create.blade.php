<x-superadmin-layout :pageTitle="isset($user) ? 'Sửa tài khoản nhân viên' : 'Tạo tài khoản nhân viên'">
    <div x-data="{
        showModal: false,
        modalTitle: '',
        modalMessage: '',
        confirmActionUrl: '',
        confirmText: 'Xác nhận',
        confirmColor: 'red',
        openModal(title, message, url, color, text) {
            this.modalTitle = title;
            this.modalMessage = message;
            this.confirmActionUrl = url;
            this.confirmColor = color;
            this.confirmText = text;
            this.showModal = true;
        }
    }">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-white">
                    {{ isset($user) ? 'Chỉnh sửa: ' . $user->name : 'Tạo tài khoản nhân viên mới' }}
                </h2>
                <p class="text-sm text-dark-400 mt-1">
                    {{ isset($user) ? 'Cập nhật thông tin, phân quyền và đặt lại mật khẩu.' : 'Tạo tài khoản cho kiểm duyệt viên hoặc quản trị viên.' }}
                </p>
            </div>
            <a href="{{ route('super.staff.index') }}"
                class="inline-flex py-2 px-4 border border-dark-700 hover:bg-dark-800 text-white rounded-xl text-sm transition-colors">
                Quay lại
            </a>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- ── Main form ── --}}
            <div class="lg:col-span-2 space-y-5">
                {{-- Account info --}}
                <div class="bg-dark-900 border border-dark-800 rounded-2xl p-6">
                    <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Thông tin tài khoản
                    </h3>

                    <form action="{{ isset($user) ? route('super.staff.update', $user) : route('super.staff.store') }}"
                        method="POST" class="space-y-4">
                        @csrf
                        @if (isset($user))
                            @method('PUT')
                        @endif

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="name" class="block text-sm font-medium text-dark-300">Họ Tên <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" autocomplete="off"
                                    class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 transition-colors"
                                    value="{{ old('name', $user->name ?? '') }}" required>
                            </div>

                            <div class="space-y-1.5">
                                <label for="email" class="block text-sm font-medium text-dark-300">Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" autocomplete="off"
                                    class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 transition-colors"
                                    value="{{ old('email', $user->email ?? '') }}" required>
                            </div>
                        </div>

                        @if (!isset($user))
                            <div class="space-y-1.5">
                                <label for="password" class="block text-sm font-medium text-dark-300">Mật khẩu <span
                                        class="text-red-500">*</span></label>
                                <input type="password" id="password" name="password" autocomplete="new-password"
                                    class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 transition-colors"
                                    placeholder="Tối thiểu 8 ký tự, có chữ hoa + số" required>
                                <p class="text-xs text-dark-500">Mật khẩu cần tối thiểu 8 ký tự, bao gồm chữ hoa, chữ
                                    thường và số.</p>
                            </div>
                        @endif

                        {{-- Role --}}
                        <div class="space-y-1.5">
                            <label for="role" class="block text-sm font-medium text-dark-300">Vai trò <span
                                    class="text-red-500">*</span></label>
                            <select id="role" name="role"
                                class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 transition-colors">
                                <option value="moderator"
                                    {{ old('role', $user->role->value ?? '') === 'moderator' ? 'selected' : '' }}>
                                    Kiểm duyệt viên
                                </option>
                                <option value="admin"
                                    {{ old('role', $user->role->value ?? '') === 'admin' ? 'selected' : '' }}>
                                    Quản trị viên
                                </option>
                            </select>
                            <p class="text-xs text-dark-500">Vai trò này quyết định cấp độ trong hệ thống, không phải
                                quyền
                                cụ thể trong Control Panel.</p>
                        </div>

                        {{-- Spatie permissions --}}
                        <div class="space-y-3 pt-2">
                            <div>
                                <p class="text-sm font-medium text-dark-300 mb-0.5">Nhóm quyền hạn Control Panel</p>
                                <p class="text-xs text-dark-500">Chọn các nhóm quyền hạn cụ thể cho phép tài khoản này
                                    truy cập tính năng nào.</p>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-2">
                                @foreach ($spatieRoles as $spatieRole)
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-xl border border-dark-800 hover:border-dark-600 hover:bg-dark-800/50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="spatie_roles[]" value="{{ $spatieRole->name }}"
                                            class="rounded border-dark-600 bg-dark-900 text-indigo-500 focus:ring-indigo-500/50"
                                            {{ (isset($user) && $user->hasRole($spatieRole->name)) || in_array($spatieRole->name, old('spatie_roles', [])) ? 'checked' : '' }}>
                                        <div>
                                            <p class="text-sm font-medium text-white">{{ $spatieRole->name }}</p>
                                            <p class="text-xs text-dark-500">{{ $spatieRole->permissions->count() }}
                                                quyền</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-4 border-t border-dark-800 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-500 text-white text-sm font-semibold rounded-xl hover:bg-indigo-600 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                {{ isset($user) ? 'Cập nhật' : 'Tạo tài khoản' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Reset password (edit only) --}}
                @if (isset($user))
                    <div class="bg-dark-900 border border-dark-800 rounded-2xl p-6">
                        <h3 class="text-base font-semibold text-white mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Đặt lại mật khẩu
                        </h3>
                        <p class="text-xs text-dark-500 mb-4">Chỉ nhập nếu muốn thay đổi mật khẩu của tài khoản này.</p>

                        <form x-ref="resetForm" action="{{ route('super.staff.resetPassword', $user) }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="new_password" class="block text-sm font-medium text-dark-300">Mật khẩu
                                        mới <span class="text-red-500">*</span></label>
                                    <input type="password" id="new_password" name="password"
                                        autocomplete="new-password"
                                        class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-amber-500 focus:border-amber-500 px-4 py-2.5 transition-colors"
                                        placeholder="Tối thiểu 8 ký tự">
                                </div>
                                <div class="space-y-1.5">
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-dark-300">Xác nhận <span
                                            class="text-red-500">*</span></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        autocomplete="new-password"
                                        class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-amber-500 focus:border-amber-500 px-4 py-2.5 transition-colors"
                                        placeholder="Nhập lại mật khẩu">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="button"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition-all"
                                    @click="openModal('Đặt lại mật khẩu', 'Bạn có chắc chắn muốn đặt lại mật khẩu cho tài khoản {{ $user->name }}?', '{{ route('super.staff.resetPassword', $user) }}', 'amber', 'Đặt lại ngay')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Đặt lại mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            {{-- ── Info panel ── --}}
            <div class="space-y-4">
                @if (isset($user))
                    <div class="bg-dark-900 border border-dark-800 rounded-2xl p-5 sticky top-20">
                        <p class="text-xs font-semibold text-dark-400 uppercase tracking-wider mb-4">Thông tin tài
                            khoản</p>

                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-dark-800">
                            <div
                                class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                                @if ($user->avatar)
                                    <img src="{{ $user->avatar }}" class="w-full h-full object-cover"
                                        alt="">
                                @else
                                    <span
                                        class="text-xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-white">{{ $user->name }}</p>
                                <p class="text-xs text-dark-500">{{ $user->email }}</p>
                                <span
                                    class="badge text-[10px] mt-1 {{ match($user->role->color()) {
                                        'gray' => 'bg-gray-500/20 text-gray-400',
                                        'blue' => 'bg-blue-500/20 text-blue-400',
                                        'amber' => 'bg-amber-500/20 text-amber-400',
                                        'red' => 'bg-red-500/20 text-red-400',
                                        default => 'bg-gray-500/20 text-gray-400'
                                    } }}">
                                    {{ $user->role->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between">
                                <span class="text-dark-500">Ngày tạo</span>
                                <span class="text-white">{{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-dark-500">Trạng thái</span>
                                @if ($user->is_active)
                                    <span class="text-emerald-400">● Hoạt động</span>
                                @else
                                    <span class="text-red-400">● Đã khóa</span>
                                @endif
                            </div>
                            <div class="flex justify-between">
                                <span class="text-dark-500">Nhóm quyền hạn</span>
                                <span class="text-white">{{ $user->roles->count() }}</span>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-dark-800">
                            <button type="button"
                                @click="openModal('{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}', 'Bạn có chắc chắn muốn {{ $user->is_active ? 'khóa' : 'mở khóa' }} tài khoản này?', '{{ route('super.staff.toggleBan', $user) }}', '{{ $user->is_active ? 'red' : 'emerald' }}', 'Xác nhận')"
                                class="w-full py-2 text-sm font-semibold rounded-xl transition-all
                                       {{ $user->is_active
                                           ? 'border border-red-500/30 text-red-400 hover:bg-red-500/10'
                                           : 'border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/10' }}">
                                {{ $user->is_active ? '🔒 Khóa tài khoản' : '✅ Mở khóa tài khoản' }}
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-dark-900 border border-amber-500/20 rounded-2xl p-5">
                        <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider mb-3">Lưu ý khi tạo tài
                            khoản</p>
                        <ul class="text-xs text-dark-400 space-y-2">
                            <li class="flex items-start gap-2"><span class="text-amber-400 shrink-0">•</span> Mật khẩu
                                phải tối thiểu 8 ký tự, bao gồm chữ hoa, thường và số.</li>
                            <li class="flex items-start gap-2"><span class="text-amber-400 shrink-0">•</span> Tài
                                khoản sẽ được xác thực email ngay lập tức.</li>
                            <li class="flex items-start gap-2"><span class="text-amber-400 shrink-0">•</span> Phân
                                quyền Control Panel thông qua nhóm quyền hạn (Spatie).</li>
                            <li class="flex items-start gap-2"><span class="text-amber-400 shrink-0">•</span> Super
                                Admin không thể bị quản lý tại đây.</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- Confirmation Modal --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-dark-950/80 backdrop-blur-sm" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" @click.away="showModal = false" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block px-4 pt-5 pb-4 text-left align-bottom transition-all transform bg-dark-900 border border-dark-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto rounded-full sm:mx-0 sm:h-10 sm:w-10"
                            :class="`bg-${confirmColor}-500/10`">
                            <svg class="w-6 h-6" :class="`text-${confirmColor}-500`"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-white" id="modal-title"
                                x-text="modalTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-dark-400" x-text="modalMessage"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        {{-- To handle password reset, we submit the actual form if it's the reset action. Since we only have POST requests, we can just point the modal form action. Wait, for reset password we need the input fields! --}}
                        <template x-if="modalTitle === 'Đặt lại mật khẩu'">
                            <button type="button" @click="$refs.resetForm.submit()"
                                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors border border-transparent rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                                :class="`bg-${confirmColor}-500 hover:bg-${confirmColor}-600 focus:ring-${confirmColor}-500`"
                                x-text="confirmText">
                            </button>
                        </template>
                        <template x-if="modalTitle !== 'Đặt lại mật khẩu'">
                            <form :action="confirmActionUrl" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors border border-transparent rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                                    :class="`bg-${confirmColor}-500 hover:bg-${confirmColor}-600 focus:ring-${confirmColor}-500`"
                                    x-text="confirmText">
                                </button>
                            </form>
                        </template>
                        <button type="button" @click="showModal = false"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium transition-colors border shadow-sm text-dark-300 bg-dark-800 border-dark-700 rounded-xl hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Hủy bỏ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-superadmin-layout>
