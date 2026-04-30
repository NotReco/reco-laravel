<x-admin-layout pageTitle="Quản lý Thành Viên">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">Chỉnh sửa: {{ $user->name }}</h2>
            <p class="text-sm text-dark-400 mt-1">Cập nhật quyền hạn, uy tín và kho đồ vật phẩm</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex py-2 px-4 border border-dark-700 hover:bg-dark-800 text-white rounded-xl text-sm transition-colors">
            Quay lại
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Cột trái: Thông tin cơ bản & Role --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-dark-900 border border-dark-800 rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Thông tin chung</h3>
                    
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-dark-800">
                        <div class="w-16 h-16 rounded-full bg-dark-800 flex items-center justify-center overflow-hidden shrink-0 border-2 border-dark-700">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-white">{{ $user->name }}</p>
                            <p class="text-sm text-dark-400">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label for="role" class="block text-sm font-medium text-dark-300">Danh xưng (Role) <span class="text-red-500">*</span></label>
                            <select name="role" id="role" class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors">
                                @php
                                    $allowedRoles = auth()->user()->role->value === 'admin'
                                        ? [\App\Enums\UserRole::USER, \App\Enums\UserRole::TESTER, \App\Enums\UserRole::MODERATOR, \App\Enums\UserRole::ADMIN]
                                        : [\App\Enums\UserRole::USER, \App\Enums\UserRole::TESTER];
                                @endphp
                                @foreach($allowedRoles as $roleOption)
                                    <option value="{{ $roleOption->value }}" {{ $user->role === $roleOption ? 'selected' : '' }}>
                                        {{ $roleOption->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="reputation_score" class="block text-sm font-medium text-dark-300">Điểm Uy Tín <span class="text-red-500">*</span></label>
                            <input type="number" id="reputation_score" name="reputation_score" 
                                   class="w-full bg-dark-950 border border-dark-800 rounded-xl text-white placeholder-dark-600 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors" 
                                   value="{{ old('reputation_score', $user->reputation_score) }}" required>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Cột phải: Inventory --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Danh hiệu --}}
                <div class="bg-dark-900 border border-dark-800 rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Kho Danh Hiệu</h3>
                    @if($titles->isEmpty())
                        <p class="text-sm text-dark-400">Chưa có danh hiệu nào trên hệ thống.</p>
                    @else
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($titles as $title)
                                <label class="flex items-start gap-3 p-3 rounded-xl border border-dark-800 hover:border-dark-700 hover:bg-dark-800/50 cursor-pointer transition-colors">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="titles[]" value="{{ $title->id }}" 
                                               class="w-4 h-4 bg-dark-900 border-dark-700 rounded text-sky-500 focus:ring-sky-600 focus:ring-offset-dark-900"
                                               {{ $user->titles->contains($title->id) ? 'checked' : '' }}>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-sm font-medium text-white flex gap-2 items-center">
                                            <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $title->color_hex }}"></span>
                                            <span class="truncate">{{ $title->name }}</span>
                                        </span>
                                        <span class="text-xs text-dark-400 mt-1 truncate">{{ $title->description ?: 'Không có mô tả' }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Khung Avatar --}}
                <div class="bg-dark-900 border border-dark-800 rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Kho Khung Avatar</h3>
                    @if($frames->isEmpty())
                        <p class="text-sm text-dark-400">Chưa có Khung Avatar nào trên hệ thống.</p>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach($frames as $frame)
                                <label class="group relative flex flex-col items-center gap-2 p-3 rounded-xl border border-dark-800 hover:border-dark-700 hover:bg-dark-800/50 cursor-pointer transition-colors">
                                    <div class="absolute top-2 right-2">
                                        <input type="checkbox" name="frames[]" value="{{ $frame->id }}" 
                                               class="w-4 h-4 bg-dark-900 border-dark-700 rounded text-sky-500 focus:ring-sky-600 focus:ring-offset-dark-900"
                                               {{ $user->frames->contains($frame->id) ? 'checked' : '' }}>
                                    </div>
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center p-1 relative">
                                        <img src="{{ Storage::url($frame->image_path) }}" alt="{{ $frame->name }}" class="w-full h-full object-contain drop-shadow-md">
                                    </div>
                                    <span class="text-xs font-medium text-white text-center line-clamp-2">{{ $frame->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-dark-800 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
                Lưu Thay Đổi
            </button>
        </div>
    </form>
</x-admin-layout>
