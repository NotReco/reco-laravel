<section>
    <header>
        <h2 class="text-xl font-display font-bold text-white">
            Cập nhật Mật khẩu
        </h2>

        <p class="mt-1 text-sm text-dark-400">
            Đảm bảo tài khoản của bạn đang sử dụng mật khẩu dài, ngẫu nhiên để giữ an toàn.
        </p>
    </header>

    <form
        id="update-password-form"
        data-unsaved-bar
        data-unsaved-title="Mật khẩu"
        method="post"
        action="{{ route('password.update') }}"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Mật khẩu hiện tại" class="text-dark-300" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full md:w-1/2 bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-500" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Mật khẩu mới" class="text-dark-300" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full md:w-1/2 bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-500" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Xác nhận mật khẩu" class="text-dark-300" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full md:w-1/2 bg-dark-900 border-dark-700 text-white rounded-xl focus:border-rose-500 focus:ring-rose-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-500" />
        </div>

        <button type="submit" class="hidden" aria-hidden="true" tabindex="-1">Lưu</button>
    </form>
</section>
