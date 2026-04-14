<section>
    <header>
        <h2 class="text-xl font-display font-bold text-gray-900">
            Thông tin Hồ sơ
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Cập nhật ảnh đại diện, email và các thông tin cá nhân của bạn.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="profile-information-form" data-unsaved-bar data-unsaved-title="Thông tin hồ sơ" method="post"
        action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-10"
        x-data="{
            removeAvatar: false,
            removeCover: false,
            avatarEdited: false,
            coverEdited: false,
            hasAvatar: {{ $user->avatar ? 'true' : 'false' }},
            hasCover: {{ $user->cover_photo ? 'true' : 'false' }},
            resetImageFields() {
                this.removeAvatar = false;
                this.removeCover = false;
                this.avatarEdited = false;
                this.coverEdited = false;
            },
            commitImageFields() {
                if (this.removeAvatar) this.hasAvatar = false;
                if (this.removeCover) this.hasCover = false;
                if (this.avatarEdited) this.hasAvatar = true;
                if (this.coverEdited) this.hasCover = true;
                this.resetImageFields();
            }
        }" @reset="resetImageFields()">
        @csrf
        @method('patch')

        <input type="hidden" name="remove_avatar" value="0" :value="removeAvatar ? '1' : '0'">
        <input type="hidden" name="remove_cover" value="0" :value="removeCover ? '1' : '0'">
        <input type="hidden" name="__avatar_edited" value="0" :value="avatarEdited ? '1' : '0'">
        <input type="hidden" name="__cover_edited" value="0" :value="coverEdited ? '1' : '0'">

        {{-- Hidden file inputs (populated by image editor modals, excluded from dirty snapshot) --}}
        <input type="file" name="avatar" class="hidden" data-unsaved-exclude @avatar-confirmed.window="removeAvatar = false; avatarEdited = true; $nextTick(() => { $el.dispatchEvent(new Event('change', { bubbles: true })) })">
        <input type="file" name="cover_photo" class="hidden" data-unsaved-exclude @cover-confirmed.window="removeCover = false; coverEdited = true; $nextTick(() => { $el.dispatchEvent(new Event('change', { bubbles: true })) })">

        <div class="space-y-10">
            {{-- ══════════════════════════════════════════════════════════════
                 NHÓM 1: Thông tin cơ bản
                 ══════════════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div class="md:col-span-1">
                    <x-input-label for="name" value="Tên hiển thị" class="text-gray-700 font-semibold" />
                    <x-text-input id="name" name="name" type="text"
                        class="mt-2 block w-full bg-white border-gray-200 text-gray-900 rounded-xl focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                        :value="old('name', $user->name)" required autocomplete="name" />
                    <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('name')" />
                </div>

                {{-- Email --}}
                <div class="md:col-span-1">
                    <x-input-label for="email" value="Email" class="text-gray-700 font-semibold" />
                    <x-text-input id="email" name="email" type="email"
                        class="mt-2 block w-full bg-white border-gray-200 text-gray-900 rounded-xl focus:border-sky-500 focus:ring-sky-500 shadow-sm invalid:border-rose-500 invalid:text-rose-600 focus:invalid:border-rose-500 focus:invalid:ring-rose-500"
                        :value="old('email', $user->email)" required autocomplete="email"
                        pattern='^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$'
                        title="Vui lòng nhập định dạng email chuẩn" />
                    <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div class="mt-2">
                            <p class="text-xs text-sky-600 bg-sky-50 px-3 py-2 rounded-lg border border-sky-100">
                                Email chưa được xác thực.
                                <button form="send-verification" class="font-bold underline hover:text-sky-700">
                                    Gửi lại mã.
                                </button>
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Pronouns --}}
                <div class="md:col-span-1">
                    <x-input-label for="pronouns" value="Đại từ nhân xưng" class="text-gray-700 font-semibold" />
                    <x-text-input id="pronouns" name="pronouns" type="text"
                        class="mt-2 block w-full bg-white border-gray-200 text-gray-900 rounded-xl focus:border-sky-500 focus:ring-sky-500 shadow-sm placeholder:italic"
                        :value="old('pronouns', $user->pronouns)" placeholder="Thêm đại từ danh xưng của bạn"
                        maxlength="100" />
                    <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('pronouns')" />
                </div>

                {{-- Location --}}
                <div class="md:col-span-1">
                    <x-input-label for="location" value="Địa điểm" class="text-gray-700 font-semibold" />
                    <x-text-input id="location" name="location" type="text"
                        class="mt-2 block w-full bg-white border-gray-200 text-gray-900 rounded-xl focus:border-sky-500 focus:ring-sky-500 shadow-sm placeholder:italic"
                        :value="old('location', $user->location)" placeholder="Việt Nam" />
                    <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('location')" />
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 NHÓM 2: Hình ảnh (Discord-style compact)
                 ══════════════════════════════════════════════════════════════ --}}
            <div class="space-y-6">
                {{-- Avatar --}}
                <div class="space-y-3">
                    <h3 class="text-base font-bold text-gray-800">Ảnh Đại Diện</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" @click="$dispatch('open-avatar-modal')" :disabled="isSaving"
                            class="px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-[#5865F2] hover:bg-[#4752c4] transition-all shadow-lg shadow-indigo-500/20 active:scale-95 disabled:opacity-50 disabled:pointer-events-none">
                            Đổi Ảnh Đại Diện
                        </button>
                        <template x-if="!removeAvatar && hasAvatar">
                            <button type="button" @click="removeAvatar = true; $nextTick(() => { document.getElementById('profile-information-form').dispatchEvent(new Event('change', { bubbles: true })) })" :disabled="isSaving"
                                class="px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-[#4E5058] hover:bg-[#404249] transition-all shadow-lg shadow-gray-500/10 active:scale-95 disabled:opacity-50 disabled:pointer-events-none">
                                Xóa Ảnh Đại Diện
                            </button>
                        </template>
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
                </div>

                <div class="h-px bg-gray-200/60"></div>

                {{-- Cover --}}
                <div class="space-y-3">
                    <h3 class="text-base font-bold text-gray-800">Ảnh Bìa</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" @click="$dispatch('open-cover-modal')" :disabled="isSaving"
                            class="px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-[#5865F2] hover:bg-[#4752c4] transition-all shadow-lg shadow-indigo-500/20 active:scale-95 disabled:opacity-50 disabled:pointer-events-none">
                            Thay Ảnh Bìa
                        </button>
                        <template x-if="!removeCover && hasCover">
                            <button type="button" @click="removeCover = true; $nextTick(() => { document.getElementById('profile-information-form').dispatchEvent(new Event('change', { bubbles: true })) })" :disabled="isSaving"
                                class="px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-[#4E5058] hover:bg-[#404249] transition-all shadow-lg shadow-gray-500/10 active:scale-95 disabled:opacity-50 disabled:pointer-events-none">
                                Xóa Ảnh Bìa
                            </button>
                        </template>
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('cover_photo')" />
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 NHÓM 3: Giới thiệu & Châm ngôn
                 ══════════════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Bio --}}
                <div class="md:col-span-1" x-data="{ bioCount: {{ strlen(old('bio', $user->bio ?? '')) }} }">
                    <div class="flex items-center justify-between">
                        <x-input-label for="bio" value="Giới thiệu bản thân"
                            class="text-gray-700 font-semibold" />
                        <span class="text-[11px] font-bold" :class="bioCount > 200 ? 'text-rose-500' : 'text-gray-400'"
                            x-text="bioCount + '/200'"></span>
                    </div>
                    <textarea id="bio" name="bio" rows="4" maxlength="200"
                        class="mt-2 block w-full bg-white border-gray-200 text-gray-900 rounded-xl focus:border-sky-500 focus:ring-sky-500 shadow-sm placeholder-gray-400 placeholder:italic resize-none"
                        @input="bioCount = $event.target.value.length">{{ old('bio', $user->bio) }}</textarea>
                    <x-input-error class="mt-1 text-rose-500 text-xs" :messages="$errors->get('bio')" />
                </div>

                {{-- Movie Quote --}}
                <div class="md:col-span-1"
                    x-data="{ quoteCount: {{ strlen(old('movie_quote', $user->movie_quote ?? '')) }} }">
                    <div class="flex items-center justify-between">
                        <x-input-label for="movie_quote" value="Châm ngôn yêu thích"
                            class="text-gray-700 font-semibold" />
                        <span class="text-[11px] font-bold"
                            :class="quoteCount > 200 ? 'text-rose-500' : 'text-gray-400'"
                            x-text="quoteCount + '/200'"></span>
                    </div>
                    <textarea id="movie_quote" name="movie_quote" rows="4" maxlength="200"
                        class="mt-2 block w-full bg-white border-gray-200 text-gray-900 rounded-xl focus:border-sky-500 focus:ring-sky-500 shadow-sm placeholder-gray-400 placeholder:italic resize-none"
                        @input="quoteCount = $event.target.value.length">{{ old('movie_quote', $user->movie_quote) }}</textarea>
                    <x-input-error class="mt-1 text-rose-500 text-xs" :messages="$errors->get('movie_quote')" />
                </div>
            </div>
        </div>

        <button type="submit" class="hidden" aria-hidden="true" tabindex="-1">Lưu</button>
    </form>
</section>
