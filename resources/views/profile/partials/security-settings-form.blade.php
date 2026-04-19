<section>
    <header class="pb-5 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-900">Bảo mật</h2>
        <p class="mt-1 text-sm text-gray-500">
            Bật/tắt bảo mật 2 lớp và tuỳ chọn lưu đăng nhập để khỏi phải nhập mã mỗi lần.
        </p>
    </header>

    <form
        id="security-settings-form"
        data-unsaved-bar
        data-unsaved-title="Bảo mật"
        method="post"
        action="{{ route('settings.security.update') }}"
        class="mt-6 space-y-3"
    >
        @csrf
        @method('patch')

        {{-- 2FA --}}
        <label class="flex items-start gap-4 p-4 rounded-2xl border border-gray-200 bg-gray-50/60 hover:bg-gray-50 hover:border-gray-300 transition-colors cursor-pointer group">
            <input type="hidden" name="two_factor_enabled" value="0" />
            <input
                type="checkbox"
                name="two_factor_enabled"
                value="1"
                @checked(old('two_factor_enabled', $user->two_factor_enabled))
                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
            />
            <span class="flex-1">
                <span class="block text-sm font-semibold text-gray-900">Bảo mật 2 lớp (2FA)</span>
                <span class="block text-sm text-gray-500 mt-0.5">Khi bật, bạn sẽ nhận mã 6 số qua email khi đăng nhập.</span>
            </span>
        </label>

        {{-- Remember 2FA --}}
        <label class="flex items-start gap-4 p-4 rounded-2xl border border-gray-200 bg-gray-50/60 hover:bg-gray-50 hover:border-gray-300 transition-colors cursor-pointer group">
            <input type="hidden" name="two_factor_remember_enabled" value="0" />
            <input
                type="checkbox"
                name="two_factor_remember_enabled"
                value="1"
                @checked(old('two_factor_remember_enabled', $user->two_factor_remember_enabled))
                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
            />
            <span class="flex-1">
                <span class="block text-sm font-semibold text-gray-900">Cho phép lưu đăng nhập</span>
                <span class="block text-sm text-gray-500 mt-0.5">Sau khi nhập mã, bạn có thể chọn "lưu đăng nhập" để lần sau khỏi nhập lại.</span>
            </span>
        </label>

        <button type="submit" class="hidden" aria-hidden="true" tabindex="-1">Lưu</button>
    </form>
</section>
