<section>
    <header>
        <h2 class="text-xl font-display font-bold text-white">
            Thông tin Hồ sơ
        </h2>

        <p class="mt-1 text-sm text-dark-400">
            Cập nhật ảnh đại diện, email và các thông tin cá nhân của bạn.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        id="profile-information-form"
        data-unsaved-bar
        data-unsaved-title="Thông tin hồ sơ"
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div>
                <x-input-label for="name" value="Tên hiển thị" class="text-dark-300" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500" :value="old('name', $user->name)" required autocomplete="name" />
                <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('name')" />
            </div>

            {{-- Email --}}
            <div>
                <x-input-label for="email" value="Email" class="text-dark-300" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-rose-400">
                            Email của bạn chưa được xác thực.
                            <button form="send-verification" class="underline text-sm text-rose-500 hover:text-rose-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                                Nhấp vào đây để gửi lại email xác thực.
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-400">
                                Một link xác thực mới đã được gửi đến email của bạn.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Gender --}}
            <div>
                <x-input-label for="gender" value="Giới tính" class="text-dark-300" />
                <select id="gender" name="gender" class="mt-1 block w-full bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500">
                    <option value="" @if(old('gender', $user->gender) == '') selected @endif>Không chọn</option>
                    <option value="male" @if(old('gender', $user->gender) == 'male') selected @endif>Nam</option>
                    <option value="female" @if(old('gender', $user->gender) == 'female') selected @endif>Nữ</option>
                    <option value="other" @if(old('gender', $user->gender) == 'other') selected @endif>Khác</option>
                </select>
                <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('gender')" />
            </div>

            {{-- Location --}}
            <div>
                <x-input-label for="location" value="Địa điểm" class="text-dark-300" />
                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500" :value="old('location', $user->location)" placeholder="Hà Nội, Việt Nam" />
                <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('location')" />
            </div>

            {{-- Website --}}
            <div class="md:col-span-2">
                <x-input-label for="website" value="Website / Liên kết" class="text-dark-300" />
                <x-text-input id="website" name="website" type="url" class="mt-1 block w-full bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500" :value="old('website', $user->website)" placeholder="https://..." />
                <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('website')" />
            </div>

            {{-- Bio --}}
            <div class="md:col-span-2">
                <x-input-label for="bio" value="Giới thiệu bản thân" class="text-dark-300" />
                <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500">{{ old('bio', $user->bio) }}</textarea>
                <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('bio')" />
            </div>

            {{-- Avatar & Cover --}}
            <div>
                <x-input-label for="avatar" value="Ảnh đại diện (Avatar)" class="text-dark-300" />
                <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full text-sm text-dark-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-rose-500 file:text-white hover:file:bg-rose-600 bg-dark-900 border border-dark-700 rounded-xl" />
                <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('avatar')" />
                @if($user->avatar)
                    <div class="mt-3">
                        <img src="{{ $user->avatar }}" alt="Current avatar" class="w-16 h-16 rounded-full object-cover border-2 border-dark-700">
                    </div>
                @endif
            </div>

            <div>
                <x-input-label for="cover_photo" value="Ảnh bìa (Cover)" class="text-dark-300" />
                <input id="cover_photo" name="cover_photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-dark-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 bg-dark-900 border border-dark-700 rounded-xl" />
                <x-input-error class="mt-2 text-rose-500" :messages="$errors->get('cover_photo')" />
                @if($user->cover_photo)
                    <div class="mt-3">
                        <img src="{{ $user->cover_photo }}" alt="Current cover" class="w-32 h-16 rounded-lg object-cover border-2 border-dark-700">
                    </div>
                @endif
            </div>

        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-dark-700/50">
            <button type="submit" class="btn-primary">Lưu thay đổi</button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-400 align-middle">
                    Đã lưu thành công.
                </p>
            @endif
        </div>
    </form>
</section>
