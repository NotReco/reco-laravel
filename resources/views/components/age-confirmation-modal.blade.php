{{--
    Age Confirmation Modal
    ─────────────────────
    Triggered by: window.dispatchEvent(new CustomEvent('open-age-modal', { detail: { onConfirm, onCancel } }))
    Confirmed flag stored in localStorage['reco_age_confirmed'] = 'true'
    z-index: 9999 — above AI widget (z-[9990]), community button (z-[9980]), floating buttons
--}}
<div x-data="{
    open: false,
    targetUrl: null,
    onConfirm: null,
    onCancel: null,

    init() {
        window.addEventListener('open-age-modal', (e) => {
            this.open = true;
            this.targetUrl = e.detail?.targetUrl ?? null;
            this.onConfirm = e.detail?.onConfirm ?? null;
            this.onCancel  = e.detail?.onCancel  ?? null;
            document.body.style.overflow = 'hidden';
        });
    },

    confirmAge() {
        localStorage.setItem('reco_age_confirmed', 'true');
        this.open = false;
        document.body.style.overflow = '';
        if (this.onConfirm) this.onConfirm();
        else if (this.targetUrl) window.location.href = this.targetUrl;
        window.dispatchEvent(new CustomEvent('age-confirmed'));
    },

    cancelAge() {
        this.open = false;
        document.body.style.overflow = '';
        if (this.onCancel) this.onCancel();
    }
}"
    x-show="open"
    x-cloak
    style="display:none;"
    class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-4 sm:p-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="age-modal-title">

    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="cancelAge()"
        class="fixed inset-0 bg-slate-950/75 backdrop-blur-md">
    </div>

    {{-- Modal panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl bg-white shadow-2xl ring-1 ring-black/10 flex flex-col"
        @click.stop>

        {{-- Header strip --}}
        <div class="flex-shrink-0 flex items-center gap-4 px-6 pt-6 pb-5 bg-gradient-to-br from-red-50 to-rose-50 rounded-t-3xl border-b border-red-100">
            {{-- Icon --}}
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            {{-- Title --}}
            <div>
                <p class="text-xs font-semibold text-red-500 uppercase tracking-widest mb-0.5">Giới hạn độ tuổi</p>
                <h3 id="age-modal-title" class="text-lg font-bold text-gray-900 leading-tight">
                    Nội dung giới hạn độ tuổi
                </h3>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 px-6 py-5">
            {{-- 18+ badge --}}
            <div class="flex justify-center mb-4">
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-red-600 text-white text-sm font-extrabold tracking-wide shadow-md shadow-red-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                    </svg>
                    18+
                </span>
            </div>

            <p class="text-sm text-gray-600 leading-relaxed text-center">
                Nội dung bạn chuẩn bị xem được phân loại <strong class="text-gray-800">dành cho khán giả từ 18&nbsp;tuổi trở lên</strong> và có thể chứa các yếu tố nhạy cảm, bạo lực hoặc nội dung không phù hợp với trẻ em.
            </p>

            <p class="text-sm text-gray-500 leading-relaxed text-center mt-3">
                Bằng việc tiếp tục, bạn xác nhận mình đã đủ <strong class="text-gray-700">18&nbsp;tuổi</strong> hoặc lớn hơn.
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex-shrink-0 flex flex-col sm:flex-row-reverse gap-3 px-6 pb-6 pt-2">
            <button
                type="button"
                @click="confirmAge()"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-red-600 hover:bg-red-500 active:bg-red-700 text-white font-semibold text-sm shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Tôi đã đủ 18 tuổi
            </button>
            <button
                type="button"
                @click="cancelAge()"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 font-semibold text-sm ring-1 ring-inset ring-gray-200 hover:ring-gray-300 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                Hủy và quay lại
            </button>
        </div>
    </div>
</div>
