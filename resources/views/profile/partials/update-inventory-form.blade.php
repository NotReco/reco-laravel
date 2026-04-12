<section>
    <header>
        <h2 class="text-xl font-display font-bold text-white">
            Kho Hành Trang
        </h2>
        <p class="mt-1 text-sm text-dark-400">
            Quản lý Trang bị (Danh hiệu, Khung ảnh đại diện) hiển thị công khai trên hồ sơ của bạn.
        </p>
    </header>

    <form id="inventory-form" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8" data-unsaved-bar data-unsaved-title="Kho hành trang">
        @csrf
        @method('patch')

        {{-- Titles --}}
        <div>
            <h3 class="text-lg font-medium text-white mb-3">Danh Hiệu</h3>
            @if($user->titles->isEmpty())
                <p class="text-sm text-dark-500 italic bg-dark-900 border border-dark-800 p-4 rounded-xl">Bạn chưa có Danh hiệu nào.</p>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-dark-800 hover:border-dark-700 hover:bg-dark-800/50 cursor-pointer transition-colors">
                        <input type="radio" name="active_title_id" value="" 
                               class="w-4 h-4 bg-dark-900 border-dark-700 text-sky-500 focus:ring-sky-600 focus:ring-offset-dark-900"
                               {{ !$user->active_title_id ? 'checked' : '' }} data-initial>
                        <span class="text-sm font-medium text-dark-300">Gỡ trang bị</span>
                    </label>

                    @foreach($user->titles as $title)
                        <label class="flex items-center gap-3 p-3 rounded-xl border {{ $user->active_title_id == $title->id ? 'border-sky-500/50 bg-sky-500/10' : 'border-dark-800 hover:border-dark-700 hover:bg-dark-800/50' }} cursor-pointer transition-colors">
                            <input type="radio" name="active_title_id" value="{{ $title->id }}" 
                                   class="w-4 h-4 bg-dark-900 border-dark-700 text-sky-500 focus:ring-sky-600 focus:ring-offset-dark-900"
                                   {{ $user->active_title_id == $title->id ? 'checked' : '' }} data-initial>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium text-white flex gap-2 items-center">
                                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $title->color_hex }}"></span>
                                    <span class="truncate">{{ $title->name }}</span>
                                </span>
                                @if($title->description)
                                    <span class="text-xs text-dark-400 mt-1 truncate">{{ $title->description }}</span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Frames --}}
        <div>
            <h3 class="text-lg font-medium text-white mb-3">Khung Avatar</h3>
            @if($user->frames->isEmpty())
                <p class="text-sm text-dark-500 italic bg-dark-900 border border-dark-800 p-4 rounded-xl">Bạn chưa có Khung Avatar nào.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4">
                    <label class="group flex flex-col items-center justify-center p-3 rounded-xl border border-dark-800 hover:border-dark-700 hover:bg-dark-800/50 cursor-pointer transition-colors">
                        <input type="radio" name="active_frame_id" value="" 
                               class="mb-3 w-4 h-4 bg-dark-900 border-dark-700 text-sky-500 focus:ring-sky-600 focus:ring-offset-dark-900"
                               {{ !$user->active_frame_id ? 'checked' : '' }} data-initial>
                        <span class="text-sm font-medium text-dark-300">Gỡ trang bị</span>
                    </label>

                    @foreach($user->frames as $frame)
                        <label class="group relative flex flex-col items-center gap-2 p-3 rounded-xl border {{ $user->active_frame_id == $frame->id ? 'border-sky-500/50 bg-sky-500/10' : 'border-dark-800 hover:border-dark-700 hover:bg-dark-800/50' }} cursor-pointer transition-colors">
                            <div class="absolute top-2 right-2 z-10">
                                <input type="radio" name="active_frame_id" value="{{ $frame->id }}" 
                                       class="w-4 h-4 bg-dark-900 border-dark-700 rounded-full text-sky-500 focus:ring-sky-600 focus:ring-offset-dark-900"
                                       {{ $user->active_frame_id == $frame->id ? 'checked' : '' }} data-initial>
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

        <button type="submit" class="hidden" aria-hidden="true" tabindex="-1">Lưu</button>
    </form>
</section>
