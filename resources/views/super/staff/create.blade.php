<x-superadmin-layout :pageTitle="isset($user) ? 'Sửa tài khoản Staff' : 'Tạo tài khoản Staff'">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">
                {{ isset($user) ? 'Chỉnh sửa: ' . $user->name : 'Tạo tài khoản Staff mới' }}
            </h2>
            <p class="text-sm text-dark-400 mt-1">
                {{ isset($user) ? 'Cập nhật thông tin, phân quyền và đặt lại mật khẩu.' : 'Tạo tài khoản cho Staff hoặc Admin.' }}
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Thông tin tài khoản
                </h3>

                <form action="{{ isset($user) ? route('super.staff.update', $user) : route('super.staff.store') }}"
                      method="POST" class="space-y-4">
                    @csrf
                    @if(isset($user)) @method('PUT') @endif

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="name" class="block text-sm font-medium text-dark-300">Họ & Tên <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name"
                                   class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 transition-colors"
                                   value="{{ old('name', $user->name ?? '') }}" required>
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="block text-sm font-medium text-dark-300">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email"
                                   class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 transition-colors"
                                   value="{{ old('email', $user->email ?? '') }}" required>
                        </div>
                    </div>

                    @if(!isset($user))
                    <div class="space-y-1.5">
                        <label for="password" class="block text-sm font-medium text-dark-300">Mật khẩu <span class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password"
                               class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 transition-colors"
                               placeholder="Tối thiểu 8 ký tự, có chữ hoa + số" required>
                        <p class="text-xs text-dark-500">Mật khẩu cần tối thiểu 8 ký tự, bao gồm chữ hoa, chữ thường và số.</p>
                    </div>
                    @endif

                    {{-- Role --}}
                    <div class="space-y-1.5">
                        <label for="role" class="block text-sm font-medium text-dark-300">Role hệ thống <span class="text-red-500">*</span></label>
                        <select id="role" name="role"
                                class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 transition-colors">
                            <option value="moderator" {{ old('role', $user->role->value ?? '') === 'moderator' ? 'selected' : '' }}>
                                Quản trị viên (Moderator)
                            </option>
                            <option value="admin" {{ old('role', $user->role->value ?? '') === 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                        </select>
                        <p class="text-xs text-dark-500">Role này quyết định cấp độ trong hệ thống, không phải quyền cụ thể trong Control Panel.</p>
                    </div>

                    {{-- Spatie permissions --}}
                    <div class="space-y-3 pt-2">
                        <div>
                            <p class="text-sm font-medium text-dark-300 mb-0.5">Nhóm quyền Control Panel</p>
                            <p class="text-xs text-dark-500">Chọn các nhóm quyền cụ thể cho phép tài khoản này truy cập tính năng nào.</p>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-2">
                            @foreach($spatieRoles as $spatieRole)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-dark-800 hover:border-dark-600 hover:bg-dark-800/50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="spatie_roles[]" value="{{ $spatieRole->name }}"
                                           class="rounded border-dark-600 bg-dark-900 text-indigo-500 focus:ring-indigo-500/50"
                                           {{ (isset($user) && $user->hasRole($spatieRole->name)) || in_array($spatieRole->name, old('spatie_roles', [])) ? 'checked' : '' }}>
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $spatieRole->name }}</p>
                                        <p class="text-xs text-dark-500">{{ $spatieRole->permissions->count() }} quyền</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-dark-800 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-500 text-white text-sm font-semibold rounded-xl hover:bg-indigo-600 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ isset($user) ? 'Cập nhật' : 'Tạo tài khoản' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Reset password (edit only) --}}
            @if(isset($user))
            <div class="bg-dark-900 border border-dark-800 rounded-2xl p-6">
                <h3 class="text-base font-semibold text-white mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Đặt lại mật khẩu
                </h3>
                <p class="text-xs text-dark-500 mb-4">Chỉ nhập nếu muốn thay đổi mật khẩu của tài khoản này.</p>

                <form action="{{ route('super.staff.resetPassword', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="new_password" class="block text-sm font-medium text-dark-300">Mật khẩu mới <span class="text-red-500">*</span></label>
                            <input type="password" id="new_password" name="password"
                                   class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-amber-500 focus:border-amber-500 px-4 py-2.5 transition-colors"
                                   placeholder="Tối thiểu 8 ký tự">
                        </div>
                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="block text-sm font-medium text-dark-300">Xác nhận <span class="text-red-500">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-amber-500 focus:border-amber-500 px-4 py-2.5 transition-colors"
                                   placeholder="Nhập lại mật khẩu">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition-all"
                                onclick="return confirm('Đặt lại mật khẩu cho {{ $user->name }}?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
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
            @if(isset($user))
            <div class="bg-dark-900 border border-dark-800 rounded-2xl p-5 sticky top-20">
                <p class="text-xs font-semibold text-dark-400 uppercase tracking-wider mb-4">Thông tin tài khoản</p>

                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-dark-800">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" class="w-full h-full object-cover" alt="">
                        @else
                            <span class="text-xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-white">{{ $user->name }}</p>
                        <p class="text-xs text-dark-500">{{ $user->email }}</p>
                        <span class="badge text-[10px] mt-1 bg-{{ $user->role->color() }}-500/20 text-{{ $user->role->color() }}-400">
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
                        @if($user->is_active)
                            <span class="text-emerald-400">● Hoạt động</span>
                        @else
                            <span class="text-red-400">● Đã khóa</span>
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark-500">Nhóm quyền</span>
                        <span class="text-white">{{ $user->roles->count() }}</span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-dark-800">
                    <form action="{{ route('super.staff.toggleBan', $user) }}" method="POST"
                          onsubmit="return confirm('{{ $user->is_active ? 'Khóa' : 'Mở khóa' }} tài khoản này?')">
                        @csrf
                        <button type="submit"
                                class="w-full py-2 text-sm font-semibold rounded-xl transition-all
                                       {{ $user->is_active
                                            ? 'border border-red-500/30 text-red-400 hover:bg-red-500/10'
                                            : 'border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/10' }}">
                            {{ $user->is_active ? '🔒 Khóa tài khoản' : '✅ Mở khóa tài khoản' }}
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-dark-900 border border-amber-500/20 rounded-2xl p-5">
                <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider mb-3">Lưu ý khi tạo tài khoản</p>
                <ul class="text-xs text-dark-400 space-y-2">
                    <li class="flex items-start gap-2"><span class="text-amber-400 shrink-0">•</span> Mật khẩu phải tối thiểu 8 ký tự, bao gồm chữ hoa, thường và số.</li>
                    <li class="flex items-start gap-2"><span class="text-amber-400 shrink-0">•</span> Tài khoản sẽ được xác thực email ngay lập tức.</li>
                    <li class="flex items-start gap-2"><span class="text-amber-400 shrink-0">•</span> Phân quyền Control Panel thông qua nhóm quyền (Spatie).</li>
                    <li class="flex items-start gap-2"><span class="text-amber-400 shrink-0">•</span> Super Admin không thể bị quản lý tại đây.</li>
                </ul>
            </div>
            @endif
        </div>
    </div>

</x-superadmin-layout>
