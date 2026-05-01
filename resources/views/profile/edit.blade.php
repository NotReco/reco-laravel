<x-app-layout>
    <x-slot:title>Hồ sơ</x-slot:title>

    <div class="min-h-screen py-12 relative overflow-hidden" x-data="unsavedChangesHub()">
        {{-- Overall Page Background Blobs --}}
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10">
            <div class="absolute top-1/4 -left-1/4 w-[800px] h-[800px] bg-sky-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-200/20 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">

            <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Chỉnh sửa hồ sơ</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Cập nhật thông tin cá nhân của bạn</p>
                    </div>
                </div>
                <a href="{{ route('profile.show', Auth::user()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Xem hồ sơ
                </a>
            </div>

            <div
                class="bg-white/80 backdrop-blur-xl border border-white/60 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] rounded-3xl p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div
                class="bg-white/80 backdrop-blur-xl border border-white/60 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] rounded-3xl p-6 sm:p-8">
                @include('profile.partials.update-inventory-form')
            </div>

            <div
                class="bg-white/80 backdrop-blur-xl border border-white/60 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] rounded-3xl p-6 sm:p-8">
                @include('profile.partials.update-top-movies-form')
            </div>

        </div>

        <div x-cloak x-show="dirty" x-transition.opacity.duration.150ms
            class="fixed left-0 right-0 bottom-0 z-[9998] px-4 pb-4">
            <div class="max-w-4xl mx-auto">
                <div
                    class="rounded-2xl border border-gray-200/50 bg-white/90 backdrop-blur-xl shadow-2xl shadow-sky-900/10 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 hover:border-gray-300/50 transition-colors">
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-900">
                            Hãy cẩn thận - bạn chưa lưu các thay đổi!
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                        <button type="button" :disabled="isSaving"
                            class="w-full sm:w-auto px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 underline-offset-4 hover:underline disabled:opacity-50"
                            x-on:click="resetAll()">
                            Đặt lại
                        </button>
                        <button type="button" :disabled="isSaving"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white transition disabled:opacity-75 disabled:cursor-wait"
                            x-on:click="save()">
                            <svg x-show="isSaving" style="display: none;" class="w-4 h-4 animate-spin outline-none" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isSaving ? 'Đang lưu...' : 'Lưu thay đổi'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         DISCORD-STYLE IMAGE EDITOR MODALS (Global scope, outside cards)
         ══════════════════════════════════════════════════════════════════════ --}}
    <div x-data="imageEditorModals()" x-cloak
         @open-avatar-modal.window="openAvatar()"
         @open-cover-modal.window="openCover()"
         @keydown.escape.window="closeModal()">

        {{-- ─── Avatar Modal ─── --}}
        <div x-show="avatarModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
             @wheel.prevent @contextmenu.prevent
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-900/70" @click="closeModal()"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden"
                 @click.stop
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-6 scale-95">
                <div class="p-6 sm:p-8">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900" x-text="avatarStep === 'edit' ? 'Chỉnh sửa hình ảnh' : 'Đổi ảnh đại diện'"></h3>
                        <button type="button" @click="closeModal()" class="p-2 text-gray-400 hover:text-gray-900 rounded-xl bg-gray-50 hover:bg-gray-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Step 1: Current preview + Upload button --}}
                    <template x-if="avatarStep === 'choose'">
                        <div class="space-y-6">
                            {{-- Current avatar preview --}}
                            <div class="flex flex-col items-center gap-4">
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Ảnh hiện tại</p>
                                <div class="w-32 h-32 rounded-full border-4 border-gray-100 shadow-lg overflow-hidden bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center">
                                    <template x-if="avatarFinalSrc">
                                        <img :src="avatarFinalSrc" alt="Avatar mới" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!avatarFinalSrc">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        @endif
                                    </template>
                                </div>
                            </div>

                            {{-- Upload button --}}
                            <label class="flex items-center justify-center gap-3 w-full py-3.5 bg-[#5865F2] hover:bg-[#4752c4] text-white font-bold rounded-xl cursor-pointer transition-all active:scale-[0.98] shadow-lg shadow-indigo-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Tải lên hình ảnh
                                <input type="file" accept="image/png,image/jpeg,image/gif,image/webp" class="sr-only" @change="handleAvatarUpload($event)">
                            </label>
                            <p class="text-xs text-gray-400 text-center">PNG, JPG, GIF hoặc WebP • Tối đa 2MB</p>
                        </div>
                    </template>

                    {{-- Step 2: Image editor --}}
                    <template x-if="avatarStep === 'edit'">
                        <div class="space-y-5" x-init="$nextTick(() => drawAvatarCanvas())">
                            {{-- Canvas preview area --}}
                            <div class="relative mx-auto bg-[#2B2D31] rounded-2xl overflow-hidden flex items-center justify-center cursor-move touch-none" style="width: 360px; height: 360px; max-width: 100%;" @mousedown="startPan($event, 'avatar')" @mousemove.window="pan($event, 'avatar')" @mouseup.window="endPan()" @touchstart="startPan($event, 'avatar')" @touchmove.prevent.window="pan($event, 'avatar')" @touchend.window="endPan()">
                                <canvas x-ref="avatarCanvas" width="360" height="360" class="block pointer-events-none"></canvas>
                            </div>

                            {{-- Controls row: zoom slider + rotate buttons --}}
                            <div class="flex items-center gap-2.5 justify-center">
                                {{-- Small image icon --}}
                                <svg class="w-4 h-4 text-gray-500 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V5zm4 3a1 1 0 100-2 1 1 0 000 2zm10 4l-3-3-2 2-3-3-4 4h12z" clip-rule="evenodd"/></svg>
                                {{-- Zoom slider --}}
                                <input type="range" min="1" max="3" step="0.01" x-model="avatarZoom" @input="drawAvatarCanvas()" class="w-36 h-1 accent-gray-800 rounded-full cursor-pointer appearance-none bg-gray-300 [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:h-3 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-gray-800 [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:shadow-none">
                                {{-- Large image icon --}}
                                <svg class="w-5 h-5 text-gray-500 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V5zm4 3a1 1 0 100-2 1 1 0 000 2zm10 4l-3-3-2 2-3-3-4 4h12z" clip-rule="evenodd"/></svg>

                                <div class="w-px h-5 bg-gray-200 shrink-0 mx-1"></div>

                                {{-- Rotate left --}}
                                <button type="button" @click="avatarRotation = (avatarRotation - 90) % 360; drawAvatarCanvas()" class="p-1.5 text-gray-800 hover:text-black rounded-lg hover:bg-gray-200 transition-all shrink-0" title="Xoay trái">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                </button>
                                {{-- Rotate right --}}
                                <button type="button" @click="avatarRotation = (avatarRotation + 90) % 360; drawAvatarCanvas()" class="p-1.5 text-gray-800 hover:text-black rounded-lg hover:bg-gray-200 transition-all shrink-0" title="Xoay phải">
                                    <svg class="w-5 h-5 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                </button>
                            </div>

                            {{-- Bottom row: Reset left, Cancel/Confirm right --}}
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <button type="button" @click="resetAvatarEditor()" class="text-sm font-semibold text-[#5865F2] hover:text-[#4752c4] transition-colors">
                                    Đặt lại
                                </button>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="avatarStep = 'choose'; avatarNewSrc = null" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                                        Hủy
                                    </button>
                                    <button type="button" @click="confirmAvatar()" class="px-6 py-2.5 text-sm font-bold text-white bg-[#5865F2] hover:bg-[#4752c4] rounded-xl transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                                        Xác nhận
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ─── Cover Modal (Advanced Crop + Resize) ─── --}}
        <div x-show="coverModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
             @wheel.prevent @contextmenu.prevent
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-900/75" @click="closeModal()"></div>
            <div class="relative bg-white rounded-3xl w-full shadow-2xl"
                 style="max-width: 700px;"
                 @click.stop
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-6 scale-95">
                <div class="p-5 sm:p-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Thay ảnh bìa</h3>
                        <button type="button" @click="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-900 rounded-xl bg-gray-100 hover:bg-gray-200 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Step 1: Current preview + Upload --}}
                    <template x-if="coverStep === 'choose'">
                        <div class="space-y-6">
                            <div class="flex flex-col items-center gap-3">
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Ảnh bìa hiện tại</p>
                                <div class="w-full rounded-2xl border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center p-2" style="height: 200px;">
                                    <template x-if="coverFinalSrc">
                                        <img :src="coverFinalSrc" alt="Cover mới" class="w-full h-full object-contain rounded-xl">
                                    </template>
                                    <template x-if="!coverFinalSrc">
                                        @if($user->cover_photo)
                                            <img src="{{ Str::startsWith($user->cover_photo, 'http') ? $user->cover_photo : asset($user->cover_photo) }}" alt="Cover" class="w-full h-full object-contain rounded-xl">
                                        @else
                                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span class="text-sm">Chưa có ảnh bìa</span>
                                            </div>
                                        @endif
                                    </template>
                                </div>
                            </div>
                            <label class="flex items-center justify-center gap-3 w-full py-3.5 bg-[#5865F2] hover:bg-[#4752c4] text-white font-bold rounded-xl cursor-pointer transition-all active:scale-[0.98] shadow-lg shadow-indigo-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Tải lên ảnh bìa mới
                                <input type="file" accept="image/png,image/jpeg,image/gif,image/webp" class="sr-only" @change="handleCoverUpload($event)">
                            </label>
                            <p class="text-xs text-gray-400 text-center">PNG, JPG, GIF hoặc WebP • Tối đa 10MB</p>
                        </div>
                    </template>

                    {{-- Step 2: Image editor for Cover --}}
                    <template x-if="coverStep === 'edit'">
                        <div class="space-y-5" x-init="$nextTick(() => drawCoverCanvas())">
                            {{-- Canvas preview area --}}
                            <div class="relative mx-auto bg-[#2B2D31] rounded-2xl overflow-hidden flex items-center justify-center cursor-move touch-none" style="width: 630px; height: 270px; max-width: 100%; aspect-ratio: 21/9;" @mousedown="startPan($event, 'cover')" @mousemove.window="pan($event, 'cover')" @mouseup.window="endPan()" @touchstart="startPan($event, 'cover')" @touchmove.prevent.window="pan($event, 'cover')" @touchend.window="endPan()">
                                <canvas x-ref="coverCanvas" width="630" height="270" class="block pointer-events-none w-full h-full object-contain"></canvas>
                            </div>

                            {{-- Controls row: zoom slider + rotate buttons --}}
                            <div class="flex items-center gap-2.5 justify-center">
                                {{-- Small image icon --}}
                                <svg class="w-4 h-4 text-gray-500 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V5zm4 3a1 1 0 100-2 1 1 0 000 2zm10 4l-3-3-2 2-3-3-4 4h12z" clip-rule="evenodd"/></svg>
                                {{-- Zoom slider --}}
                                <input type="range" min="1" max="3" step="0.01" x-model="coverZoom" @input="drawCoverCanvas()" class="w-36 h-1 accent-gray-800 rounded-full cursor-pointer appearance-none bg-gray-300 [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:h-3 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-gray-800 [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:shadow-none">
                                {{-- Large image icon --}}
                                <svg class="w-5 h-5 text-gray-500 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V5zm4 3a1 1 0 100-2 1 1 0 000 2zm10 4l-3-3-2 2-3-3-4 4h12z" clip-rule="evenodd"/></svg>

                                <div class="w-px h-5 bg-gray-200 shrink-0 mx-1"></div>

                                {{-- Rotate left --}}
                                <button type="button" @click="coverRotation = (coverRotation - 90) % 360; drawCoverCanvas()" class="p-1.5 text-gray-800 hover:text-black rounded-lg hover:bg-gray-200 transition-all shrink-0" title="Xoay trái">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                </button>
                                {{-- Rotate right --}}
                                <button type="button" @click="coverRotation = (coverRotation + 90) % 360; drawCoverCanvas()" class="p-1.5 text-gray-800 hover:text-black rounded-lg hover:bg-gray-200 transition-all shrink-0" title="Xoay phải">
                                    <svg class="w-5 h-5 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                </button>
                            </div>

                            {{-- Bottom row: Reset left, Cancel/Confirm right --}}
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <button type="button" @click="resetCoverEditor()" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                                    Đặt lại
                                </button>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="coverStep = 'choose'; coverNewSrc = null" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                                        Hủy
                                    </button>
                                    <button type="button" @click="confirmCover()" class="px-6 py-2.5 text-sm font-bold text-white bg-[#5865F2] hover:bg-[#4752c4] rounded-xl transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                                        Xác nhận
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                // ═══════════════════════════════════════════
                // Image Editor Modals Alpine component
                // ═══════════════════════════════════════════
                Alpine.data('imageEditorModals', () => ({
                    // Modal visibility
                    avatarModal: false,
                    coverModal: false,
                    // Steps: 'choose' | 'edit'
                    avatarStep: 'choose',
                    coverStep: 'choose',
                    // Editor state — Avatar
                    avatarNewSrc: null,
                    avatarFinalSrc: null,
                    avatarImg: null,
                    avatarZoom: 1,
                    avatarRotation: 0,
                    avatarPanX: 0,
                    avatarPanY: 0,
                    avatarFile: null,
                    // Editor state — Cover
                    coverNewSrc: null,
                    coverFinalSrc: null,
                    coverImg: null,
                    coverZoom: 1,
                    coverRotation: 0,
                    coverPanX: 0,
                    coverPanY: 0,
                    coverFile: null,

                    // Crop box state (values in % of stage dimensions 0-1)
                    crop: { x: 0.05, y: 0.1, w: 0.9, h: 0.8 },
                    _cropAction: null,   // 'move' | 'resize-nw' | 'resize-ne' | 'resize-sw' | 'resize-se' | 'resize-n' | 'resize-s' | 'resize-e' | 'resize-w'
                    _cropDragStart: null,
                    _cropAtDragStart: null,
                    cropHandles: [
                        { id: 'nw', cx: 0,   cy: 0   },
                        { id: 'ne', cx: 1,   cy: 0   },
                        { id: 'sw', cx: 0,   cy: 1   },
                        { id: 'se', cx: 1,   cy: 1   },
                        { id: 'n',  cx: 0.5, cy: 0   },
                        { id: 's',  cx: 0.5, cy: 1   },
                        { id: 'w',  cx: 0,   cy: 0.5 },
                        { id: 'e',  cx: 1,   cy: 0.5 },
                    ],

                    isPanning: false,
                    panStartX: 0,
                    panStartY: 0,
                    panType: null,

                    // ─── Panning logic ───
                    startPan(e, type) {
                        this.isPanning = true;
                        this.panType = type;
                        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                        this.panStartX = clientX;
                        this.panStartY = clientY;
                    },
                    pan(e, activeType) {
                        if (!this.isPanning || this.panType !== activeType) return;
                        
                        const canvasEl = this.panType === 'cover' ? this.$refs.coverCanvas : this.$refs.avatarCanvas;
                        if (!canvasEl) return;
                        const rect = canvasEl.getBoundingClientRect();
                        const scaleScale = canvasEl.width / rect.width;
                        
                        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                        const screenDx = clientX - this.panStartX;
                        const screenDy = clientY - this.panStartY;
                        
                        // Chuyển đổi khoảng cách di chuyển từ màn hình sang pixel nội bộ của Canvas
                        const dx = screenDx * scaleScale;
                        const dy = screenDy * scaleScale;

                        if (this.panType === 'avatar') {
                            const rad = (this.avatarRotation * Math.PI) / -180;
                            const rdx = dx * Math.cos(rad) - dy * Math.sin(rad);
                            const rdy = dx * Math.sin(rad) + dy * Math.cos(rad);
                            
                            this.avatarPanX += rdx;
                            this.avatarPanY += rdy;
                            this.drawAvatarCanvas();
                        } else {
                            const rad = (this.coverRotation * Math.PI) / -180;
                            const rdx = dx * Math.cos(rad) - dy * Math.sin(rad);
                            const rdy = dx * Math.sin(rad) + dy * Math.cos(rad);
                            
                            this.coverPanX += rdx;
                            this.coverPanY += rdy;
                            this.drawCoverCanvas();
                        }
                        
                        this.panStartX = clientX;
                        this.panStartY = clientY;
                    },
                    endPan() {
                        this.isPanning = false;
                        this.panType = null;
                    },

                    // ─── Open/Close ───
                    openAvatar() {
                        this.avatarModal = true;
                        this.avatarStep = 'choose';
                        this.avatarNewSrc = null;
                        this.avatarZoom = 1;
                        this.avatarRotation = 0;
                        this.avatarPanX = 0;
                        this.avatarPanY = 0;
                        document.body.classList.add('overflow-hidden');
                    },
                    openCover() {
                        this.coverModal = true;
                        this.coverStep = 'choose';
                        this.coverNewSrc = null;
                        this.coverZoom = 1;
                        this.coverRotation = 0;
                        this.coverPanX = 0;
                        this.coverPanY = 0;
                        document.body.classList.add('overflow-hidden');
                    },
                    closeModal() {
                        this.avatarModal = false;
                        this.coverModal = false;
                        document.body.classList.remove('overflow-hidden');
                    },

                    // ─── Avatar Upload & Editor ───
                    handleAvatarUpload(e) {
                        const file = e.target.files[0];
                        if (!file) return;
                        this.avatarFile = file;
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            this.avatarNewSrc = ev.target.result;
                            this.avatarImg = new Image();
                            this.avatarImg.onload = () => {
                                this.avatarZoom = 1;
                                this.avatarRotation = 0;
                                this.avatarStep = 'edit';
                                this.$nextTick(() => this.drawAvatarCanvas());
                            };
                            this.avatarImg.src = ev.target.result;
                        };
                        reader.readAsDataURL(file);
                        e.target.value = '';
                    },

                    drawAvatarCanvas() {
                        const canvas = this.$refs.avatarCanvas;
                        if (!canvas || !this.avatarImg) return;
                        const ctx = canvas.getContext('2d');
                        const W = canvas.width, H = canvas.height;
                        const img = this.avatarImg;

                        // Clear
                        ctx.clearRect(0, 0, W, H);
                        ctx.fillStyle = '#2B2D31';
                        ctx.fillRect(0, 0, W, H);

                        // The visible circle radius (80% of canvas half)
                        const circleR = Math.min(W, H) * 0.4;
                        const circleDiam = circleR * 2;

                        // At zoom=1, image should exactly fill the circle
                        let isRotated = (this.avatarRotation % 180 !== 0);
                        let imgW = isRotated ? img.height : img.width;
                        let imgH = isRotated ? img.width : img.height;
                        const baseScale = Math.max(circleDiam / imgW, circleDiam / imgH);

                        const finalScale = baseScale * this.avatarZoom;
                        const dw = img.width * finalScale;
                        const dh = img.height * finalScale;

                        // Clamp pan to not go beyond bounds
                        let effCropW = isRotated ? circleDiam : circleDiam;
                        let effCropH = isRotated ? circleDiam : circleDiam;
                        let maxPanX = Math.max(0, Math.abs(dw - effCropW) / 2);
                        let maxPanY = Math.max(0, Math.abs(dh - effCropH) / 2);
                        this.avatarPanX = Math.max(-maxPanX, Math.min(maxPanX, this.avatarPanX));
                        this.avatarPanY = Math.max(-maxPanY, Math.min(maxPanY, this.avatarPanY));

                        ctx.clearRect(0, 0, W, H);

                        // 1. Blurred background
                        ctx.save();
                        ctx.translate(W / 2, H / 2);
                        ctx.rotate((this.avatarRotation * Math.PI) / 180);
                        ctx.filter = 'blur(16px) brightness(0.4)';
                        ctx.drawImage(img, (-dw / 2) + this.avatarPanX, (-dh / 2) + this.avatarPanY, dw, dh);
                        ctx.restore();

                        // 2. Clear image inside circle
                        ctx.save();
                        ctx.beginPath();
                        ctx.arc(W / 2, H / 2, circleR, 0, Math.PI * 2);
                        ctx.clip();
                        
                        ctx.translate(W / 2, H / 2);
                        ctx.rotate((this.avatarRotation * Math.PI) / 180);
                        ctx.drawImage(img, (-dw / 2) + this.avatarPanX, (-dh / 2) + this.avatarPanY, dw, dh);
                        ctx.restore();

                        // 3. Dashed border
                        ctx.save();
                        ctx.beginPath();
                        ctx.arc(W / 2, H / 2, circleR, 0, Math.PI * 2);
                        ctx.strokeStyle = 'rgba(255, 255, 255, 0.9)';
                        ctx.lineWidth = 2;
                        ctx.setLineDash([8, 6]);
                        ctx.stroke();

                        // 4. Center Anchor Icon
                        ctx.setLineDash([]);
                        ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
                        ctx.beginPath();
                        ctx.arc(W / 2, H / 2, 16, 0, Math.PI * 2);
                        ctx.fill();

                        ctx.strokeStyle = '#333';
                        ctx.lineWidth = 1.5;
                        ctx.beginPath();
                        const cX = W / 2, cY = H / 2;
                        const s = 6;
                        ctx.moveTo(cX - s, cY); ctx.lineTo(cX + s, cY);
                        ctx.moveTo(cX - s, cY); ctx.lineTo(cX - s + 3, cY - 3);
                        ctx.moveTo(cX - s, cY); ctx.lineTo(cX - s + 3, cY + 3);
                        ctx.moveTo(cX + s, cY); ctx.lineTo(cX + s - 3, cY - 3);
                        ctx.moveTo(cX + s, cY); ctx.lineTo(cX + s - 3, cY + 3);
                        ctx.moveTo(cX, cY - s); ctx.lineTo(cX, cY + s);
                        ctx.moveTo(cX, cY - s); ctx.lineTo(cX - 3, cY - s + 3);
                        ctx.moveTo(cX, cY - s); ctx.lineTo(cX + 3, cY - s + 3);
                        ctx.moveTo(cX, cY + s); ctx.lineTo(cX - 3, cY + s - 3);
                        ctx.moveTo(cX, cY + s); ctx.lineTo(cX + 3, cY + s - 3);
                        ctx.stroke();
                        ctx.restore();
                    },

                    resetAvatarEditor() {
                        this.avatarZoom = 1;
                        this.avatarRotation = 0;
                        this.avatarPanX = 0;
                        this.avatarPanY = 0;
                        this.drawAvatarCanvas();
                    },

                    confirmAvatar() {
                        const size = 256;
                        const img = this.avatarImg;
                        if (!img) return;

                        const offscreen = document.createElement('canvas');
                        offscreen.width = size;
                        offscreen.height = size;
                        const ctx = offscreen.getContext('2d');

                        // Fill background white (for transparency)
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, size, size);

                        // Clip to circle
                        ctx.save();
                        ctx.beginPath();
                        ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
                        ctx.clip();

                        ctx.translate(size / 2, size / 2);
                        ctx.rotate((this.avatarRotation * Math.PI) / 180);

                        let isRotated = (this.avatarRotation % 180 !== 0);
                        let imgW = isRotated ? img.height : img.width;
                        let imgH = isRotated ? img.width : img.height;
                        const baseScale = Math.max(size / imgW, size / imgH);
                        const finalScale = baseScale * this.avatarZoom;
                        const dw = img.width * finalScale;
                        const dh = img.height * finalScale;
                        
                        // Scale the panning offset from Preview Canvas size to Export Canvas size
                        const previewCropSize = 360 * 0.8; // 288 is the circle diameter in drawAvatarCanvas
                        const panRatio = size / previewCropSize;
                        
                        ctx.drawImage(img, (-dw / 2) + (this.avatarPanX * panRatio), (-dh / 2) + (this.avatarPanY * panRatio), dw, dh);
                        ctx.restore();

                        offscreen.toBlob((blob) => {
                            if (!blob) return;
                            const file = new File([blob], 'avatar.png', { type: 'image/png' });
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            const form = document.getElementById('profile-information-form');
                            const input = form.querySelector('input[name=avatar]');
                            input.files = dt.files;
                            this.avatarFinalSrc = URL.createObjectURL(blob);
                            this.$dispatch('avatar-confirmed');
                            this.closeModal();
                        }, 'image/png');
                    },

                    // ─── Cover Upload & Discord-style Canvas Editor ───
                    handleCoverUpload(e) {
                        const file = e.target.files[0];
                        if (!file) return;
                        this.coverFile = file;
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            this.coverNewSrc = ev.target.result;
                            this.coverImg = new Image();
                            this.coverImg.onload = () => {
                                this.coverZoom = 1;
                                this.coverRotation = 0;
                                this.coverPanX = 0;
                                this.coverPanY = 0;
                                this.coverStep = 'edit';
                                this.$nextTick(() => this.drawCoverCanvas());
                            };
                            this.coverImg.src = ev.target.result;
                        };
                        reader.readAsDataURL(file);
                        e.target.value = '';
                    },

                    drawCoverCanvas() {
                        const canvas = this.$refs.coverCanvas;
                        if (!canvas || !this.coverImg) return;
                        const ctx = canvas.getContext('2d');
                        const W = canvas.width, H = canvas.height;
                        const img = this.coverImg;

                        // Clear
                        ctx.clearRect(0, 0, W, H);
                        ctx.fillStyle = '#2B2D31'; // Dark background behind
                        ctx.fillRect(0, 0, W, H);

                        let isRotated = (this.coverRotation % 180 !== 0);
                        let imgW = isRotated ? img.height : img.width;
                        let imgH = isRotated ? img.width : img.height;
                        
                        // Scale to cover the entire canvas
                        const baseScale = Math.max(W / imgW, H / imgH);
                        const finalScale = baseScale * this.coverZoom;
                        const dw = img.width * finalScale;
                        const dh = img.height * finalScale;

                        // Clamp pan
                        let maxPanX = Math.max(0, Math.abs(dw - W) / 2);
                        let maxPanY = Math.max(0, Math.abs(dh - H) / 2);
                        this.coverPanX = Math.max(-maxPanX, Math.min(maxPanX, this.coverPanX));
                        this.coverPanY = Math.max(-maxPanY, Math.min(maxPanY, this.coverPanY));

                        ctx.save();
                        // 1. Clear inside clip 
                        ctx.beginPath();
                        ctx.rect(0, 0, W, H);
                        ctx.clip();
                        
                        ctx.translate(W / 2, H / 2);
                        ctx.rotate((this.coverRotation * Math.PI) / 180);
                        ctx.drawImage(img, (-dw / 2) + this.coverPanX, (-dh / 2) + this.coverPanY, dw, dh);
                        ctx.restore();

                        // 2. Grid guide (3x3)
                        ctx.save();
                        ctx.beginPath();
                        ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
                        ctx.lineWidth = 1;
                        ctx.setLineDash([4, 4]);
                        
                        // verticals
                        ctx.moveTo(W / 3, 0); ctx.lineTo(W / 3, H);
                        ctx.moveTo((W / 3) * 2, 0); ctx.lineTo((W / 3) * 2, H);
                        
                        // horizontals
                        ctx.moveTo(0, H / 3); ctx.lineTo(W, H / 3);
                        ctx.moveTo(0, (H / 3) * 2); ctx.lineTo(W, (H / 3) * 2);
                        ctx.stroke();

                        // 3. Center Anchor Icon
                        ctx.setLineDash([]);
                        ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
                        ctx.beginPath();
                        ctx.arc(W / 2, H / 2, 12, 0, Math.PI * 2);
                        ctx.fill();

                        ctx.strokeStyle = '#333';
                        ctx.lineWidth = 1.5;
                        ctx.beginPath();
                        const cX = W / 2, cY = H / 2;
                        const s = 4;
                        ctx.moveTo(cX - s, cY); ctx.lineTo(cX + s, cY);
                        ctx.moveTo(cX, cY - s); ctx.lineTo(cX, cY + s);
                        ctx.stroke();
                        ctx.restore();
                    },

                    resetCoverEditor() {
                        this.coverZoom = 1;
                        this.coverRotation = 0;
                        this.coverPanX = 0;
                        this.coverPanY = 0;
                        this.drawCoverCanvas();
                    },

                    confirmCover() {
                        const W = 1050; // Export width for Cover
                        const H = 450;  // 21:9 ratio (1050 / 2.333...)
                        const img = this.coverImg;
                        if (!img) return;

                        const offscreen = document.createElement('canvas');
                        offscreen.width = W;
                        offscreen.height = H;
                        const ctx = offscreen.getContext('2d');

                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, W, H);

                        ctx.save();
                        ctx.rect(0, 0, W, H);
                        ctx.clip();

                        ctx.translate(W / 2, H / 2);
                        ctx.rotate((this.coverRotation * Math.PI) / 180);

                        let isRotated = (this.coverRotation % 180 !== 0);
                        let imgW = isRotated ? img.height : img.width;
                        let imgH = isRotated ? img.width : img.height;
                        const baseScale = Math.max(W / imgW, H / imgH);
                        const finalScale = baseScale * this.coverZoom;
                        const dw = img.width * finalScale;
                        const dh = img.height * finalScale;
                        
                        const panRatio = W / 630;
                        
                        ctx.drawImage(img, (-dw / 2) + (this.coverPanX * panRatio), (-dh / 2) + (this.coverPanY * panRatio), dw, dh);
                        ctx.restore();

                        offscreen.toBlob((blob) => {
                            if (!blob) return;
                            const file = new File([blob], 'cover.png', { type: 'image/png' });
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            const form = document.getElementById('profile-information-form');
                            const input = form.querySelector('input[name=cover_photo]');
                            if (input) input.files = dt.files;
                            this.coverFinalSrc = URL.createObjectURL(blob);
                            this.$dispatch('cover-confirmed');
                            this.closeModal();
                        }, 'image/png');
                    },
                }));

                // ═══════════════════════════════════════════
                // Unsaved Changes Hub (AJAX Support)
                // ═══════════════════════════════════════════
                Alpine.data('unsavedChangesHub', () => ({
                    dirtyFormIds: new Set(),
                    forms: [],
                    isSaving: false,
                    init() {
                        this.forms = Array.from(document.querySelectorAll('form[data-unsaved-bar]'));

                        // Restore scroll position if we just came back from a form submit
                        const savedScroll = sessionStorage.getItem('profileEditScroll');
                        if (savedScroll !== null) {
                            requestAnimationFrame(() => {
                                window.scrollTo({ top: parseInt(savedScroll), behavior: 'instant' });
                                sessionStorage.removeItem('profileEditScroll');
                            });
                        }

                        for (const form of this.forms) {
                            const mark = () => this._recomputeDirty(form);
                            form.addEventListener('input', mark, {
                                passive: true
                            });
                            form.addEventListener('change', mark, {
                                passive: true
                            });
                            form.addEventListener('submit', () => this.dirtyFormIds.delete(form.id));
                        }

                        // Delay initial snapshot so Alpine has finished all :value bindings
                        requestAnimationFrame(() => {
                            for (const form of this.forms) {
                                form._unsavedInitial = this._snapshot(form);
                            }
                        });
                    },
                    get dirty() {
                        return this.dirtyFormIds.size > 0;
                    },
                    _snapshot(form) {
                        const excludeNames = new Set();
                        for (const el of form.querySelectorAll('[data-unsaved-exclude]')) {
                            if (el.name) excludeNames.add(el.name);
                        }
                        const fd = new FormData(form);
                        const entries = [];
                        for (const [k, v] of fd.entries()) {
                            if (excludeNames.has(k)) continue;
                            // Skip file inputs entirely — they are managed via modals
                            if (v instanceof File) continue;
                            entries.push([k, String(v)]);
                        }
                        for (const el of form.querySelectorAll('input[type="checkbox"][name]')) {
                            if (!el.checked) entries.push([el.name, '__unchecked__']);
                        }
                        entries.sort((x, y) => (x[0] + '=' + x[1]).localeCompare(y[0] + '=' + y[1]));
                        return JSON.stringify(entries);
                    },
                    _recomputeDirty(form) {
                        if (!form.id) return;
                        const now = this._snapshot(form);
                        const initial = form._unsavedInitial ?? now;
                        const isDirty = now !== initial;
                        if (isDirty) this.dirtyFormIds.add(form.id);
                        else this.dirtyFormIds.delete(form.id);
                    },
                    resetAll() {
                        for (const form of this.forms) {
                            if (this.dirtyFormIds.has(form.id)) {
                                form.reset();
                                this.dirtyFormIds.delete(form.id);
                            }
                        }
                    },
                    save() {
                        const target = this.forms.find(f => this.dirtyFormIds.has(f.id));
                        if (!target || this.isSaving) return;
                        
                        // Check validation before attempting submit, handles native required/pattern/email attributes
                        if (!target.reportValidity()) return;

                        // Lock the UI to prevent spam clicks during the form submission/page reload
                        this.isSaving = true;
                        
                        // Save the current scroll position so we can instantly restore it after page reload
                        sessionStorage.setItem('profileEditScroll', window.scrollY);
                        
                        // Standard form submission (reloads the page, ensuring all Blade data is fresh)
                        if (typeof target.requestSubmit === 'function') {
                            target.requestSubmit();
                        } else {
                            target.submit();
                        }
                    },
                }));
            });
        </script>
    @endpush
</x-app-layout>
