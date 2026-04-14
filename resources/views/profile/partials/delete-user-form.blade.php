<section class="space-y-6">
    <header>
        <h2 class="text-xl font-display font-bold text-rose-600">
            Xóa Tài khoản
        </h2>

        <p class="mt-1 text-sm text-rose-700/80">
            Một khi tài khoản của bạn bị xóa, toàn bộ dữ liệu và phim liên quan sẽ bị xóa vĩnh viễn. Vui lòng suy nghĩ kỹ và tải xuống bất cứ thông tin nào bạn muốn lưu giữ.
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="inline-flex items-center px-6 py-2.5 bg-rose-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-rose-500 active:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm shadow-rose-500/30">
        Xóa Tài khoản
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 bg-white border-t-4 border-rose-500 rounded-2xl shadow-2xl">
            @csrf
            @method('delete')

            <h2 class="text-xl font-display font-bold text-gray-900">
                Bạn có chắc chắn muốn xóa tài khoản này?
            </h2>

            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                Toàn bộ dữ liệu đánh giá phim, bình luận, và danh sách phim của bạn sẽ bị xóa ngay lập tức. Vui lòng nhập mật khẩu để xác nhận.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Mật khẩu" class="sr-only" />

                <x-text-input id="password" name="password" type="password" class="mt-1.5 block w-full bg-white border-gray-200 text-gray-900 rounded-xl focus:border-rose-500 focus:ring-rose-500 shadow-sm" placeholder="Mật khẩu của bạn..." />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-rose-500" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                    Hủy
                </button>

                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-rose-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-rose-500 active:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm shadow-rose-500/30">
                    Xác nhận Xóa
                </button>
            </div>
        </form>
    </x-modal>
</section>
