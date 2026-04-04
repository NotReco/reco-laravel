<section class="space-y-6">
    <header>
        <h2 class="text-xl font-display font-bold text-red-500">
            Xóa Tài khoản
        </h2>

        <p class="mt-1 text-sm text-dark-300">
            Một khi tài khoản của bạn bị xóa, toàn bộ dữ liệu và phim liên quan sẽ bị xóa vĩnh viễn. Vui lòng suy nghĩ kỹ và tải xuống bất cứ thông tin nào bạn muốn lưu giữ.
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="btn-danger inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-dark-800 transition ease-in-out duration-150">
        Xóa Tài khoản
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-dark-800 border-t-4 border-red-500">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-white">
                Bạn có chắc chắn muốn xóa tài khoản này?
            </h2>

            <p class="mt-1 text-sm text-dark-400">
                Toàn bộ dữ liệu đánh giá phim, bình luận, và danh sách phim của bạn sẽ bị xóa ngay lập tức. Vui lòng nhập mật khẩu để xác nhận.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Mật khẩu" class="sr-only" />

                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full bg-dark-900 border-dark-700 text-white rounded-xl focus:border-red-500 focus:ring-red-500" placeholder="Mật khẩu của bạn..." />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-500" />
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="btn-ghost mr-3">
                    Hủy
                </button>

                <button type="submit" class="btn-danger px-4 py-2 bg-red-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-dark-800 transition ease-in-out duration-150">
                    Xác nhận Xóa
                </button>
            </div>
        </form>
    </x-modal>
</section>
