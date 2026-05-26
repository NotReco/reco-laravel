<div x-data="{
    open: false,
    onConfirm: null,
    onCancel: null,

    init() {
        window.addEventListener('open-age-modal', (e) => {
            this.open = true;
            this.onConfirm = e.detail?.onConfirm;
            this.onCancel = e.detail?.onCancel;
            document.body.style.overflow = 'hidden';
        });
    },

    confirmAge() {
        localStorage.setItem('reco_age_confirmed', 'true');
        this.open = false;
        document.body.style.overflow = '';
        if (this.onConfirm) {
            this.onConfirm();
        }
        window.dispatchEvent(new CustomEvent('age-confirmed'));
    },

    cancelAge() {
        this.open = false;
        document.body.style.overflow = '';
        if (this.onCancel) {
            this.onCancel();
        }
    }
}" x-show="open" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog"
    aria-modal="true" x-cloak>
    
    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/80 backdrop-blur-md transition-opacity"></div>

    {{-- Modal Panel --}}
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="cancelAge()"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                
                {{-- Decorative Header --}}
                <div class="bg-red-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-red-100">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10 shadow-sm border border-red-200">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-xl font-bold leading-6 text-gray-900" id="modal-title">Nội dung giới hạn độ tuổi</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Nội dung bạn chuẩn bị truy cập được đánh giá dành cho khán giả trưởng thành (18+) và có thể chứa yếu tố nhạy cảm, bạo lực hoặc ngôn từ không phù hợp với trẻ em.
                                </p>
                                <p class="text-sm text-gray-600 font-medium mt-3">
                                    Bằng việc tiếp tục, bạn xác nhận mình đã đủ 18 tuổi hoặc lớn hơn.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Actions --}}
                <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100 gap-2">
                    <button type="button" @click="confirmAge()"
                        class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-red-500/30 hover:bg-red-500 hover:shadow-lg hover:-translate-y-0.5 transition-all sm:w-auto">
                        Tôi đã đủ 18 tuổi
                    </button>
                    <button type="button" @click="cancelAge()"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                        Hủy và quay lại
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
