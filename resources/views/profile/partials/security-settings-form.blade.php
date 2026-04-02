<section>
    <header>
        <h2 class="text-xl font-display font-bold text-white">
            Bảo mật
        </h2>

        <p class="mt-1 text-sm text-dark-400">
            Bật/tắt bảo mật 2 lớp và tuỳ chọn lưu đăng nhập để khỏi phải nhập mã mỗi lần.
        </p>
    </header>

    <form
        id="security-settings-form"
        data-unsaved-bar
        data-unsaved-title="Bảo mật"
        method="post"
        action="{{ route('settings.security.update') }}"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('patch')

        <div class="space-y-4">
            <label class="flex items-start gap-3 p-4 rounded-xl bg-dark-900/40 border border-dark-700">
                <input type="hidden" name="two_factor_enabled" value="0" />
                <input
                    type="checkbox"
                    name="two_factor_enabled"
                    value="1"
                    @checked(old('two_factor_enabled', $user->two_factor_enabled))
                    class="mt-1 rounded border-dark-700 bg-dark-900 text-rose-500 focus:ring-rose-500"
                />
                <span>
                    <span class="block font-semibold text-white">Bảo mật 2 lớp (2FA)</span>
                    <span class="block text-sm text-dark-400">Khi bật, bạn sẽ nhận mã 6 số qua email khi đăng nhập.</span>
                </span>
            </label>

            <label class="flex items-start gap-3 p-4 rounded-xl bg-dark-900/40 border border-dark-700">
                <input type="hidden" name="two_factor_remember_enabled" value="0" />
                <input
                    type="checkbox"
                    name="two_factor_remember_enabled"
                    value="1"
                    @checked(old('two_factor_remember_enabled', $user->two_factor_remember_enabled))
                    class="mt-1 rounded border-dark-700 bg-dark-900 text-rose-500 focus:ring-rose-500"
                />
                <span>
                    <span class="block font-semibold text-white">Cho phép lưu đăng nhập</span>
                    <span class="block text-sm text-dark-400">Sau khi nhập mã, bạn có thể chọn “lưu đăng nhập” để lần sau khỏi nhập lại.</span>
                </span>
            </label>
        </div>

        <button type="submit" class="hidden" aria-hidden="true" tabindex="-1">Lưu</button>
    </form>
</section>

