<section>
    <header>
        <h2 class="text-xl font-display font-bold text-white">
            Cập nhật Mật khẩu
        </h2>

        <p class="mt-1 text-sm text-dark-400">
            Đảm bảo tài khoản của bạn đang sử dụng mật khẩu dài, ngẫu nhiên để giữ an toàn.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
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

        <div class="flex items-center gap-4 pt-4 border-t border-dark-700/50">
            <button type="submit" class="btn-primary">Đổi mật khẩu</button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-400 align-middle">
                    Đã lưu thành công.
                </p>
            @endif
        </div>
    </form>
</section>
