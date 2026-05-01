<div x-data="{
    reportModal: false,
    reportTargetType: '',
    reportTargetId: null,
    selectedReason: '',
    customReason: '',
    isPublicReport: false,
    submittingReport: false,

    // Inline toast state
    toastShow: false,
    toastSuccess: true,
    toastMessage: '',
    toastTimer: null,

    reportReasons: [
        'Ngôn từ thù ghét / Quấy rối',
        'Spam hoặc lừa đảo',
        'Tiết lộ nội dung (Spoiler)',
        'Thông tin sai lệch',
        'Nội dung phản cảm',
        'Khác'
    ],

    openReport(type, id) {
        this.reportTargetType = type;
        this.reportTargetId = id;
        this.selectedReason = '';
        this.customReason = '';
        this.isPublicReport = false;
        this.reportModal = true;
    },

    showToast(message, success = true) {
        this.toastMessage = message;
        this.toastSuccess = success;
        this.toastShow = true;
        clearTimeout(this.toastTimer);
        this.toastTimer = setTimeout(() => { this.toastShow = false; }, 4000);
    },

    async submitReport() {
        if (!this.selectedReason || this.submittingReport) return;

        const finalReason = this.selectedReason;
        this.submittingReport = true;

        try {
            const res = await fetch('/api/reports', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reportable_type: this.reportTargetType,
                    reportable_id: this.reportTargetId,
                    reason: finalReason,
                    description: this.customReason,
                    is_public: this.isPublicReport
                }),
            });

            const data = await res.json();

            this.reportModal = false;
            this.selectedReason = '';
            this.customReason = '';
            this.isPublicReport = false;

            this.showToast(data.message || (data.success ? 'Đã gửi báo cáo thành công!' : 'Có lỗi xảy ra.'), data.success !== false);
        } catch (err) {
            console.error('Error reporting:', err);
            this.reportModal = false;
            this.showToast('Không thể gửi báo cáo. Vui lòng thử lại.', false);
        } finally {
            this.submittingReport = false;
        }
    }
}"
@open-report.window="openReport($event.detail.type, $event.detail.id)">

    {{-- ── Report Modal ──────────────────────────────────────────────── --}}
    <div x-show="reportModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         @click.self="reportModal = false"
         style="display: none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             @click.stop>

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Báo cáo nội dung</h3>
                <p class="text-xs text-gray-400 mt-0.5">Chọn lý do báo cáo</p>
            </div>

            {{-- Reasons --}}
            <div class="px-5 py-3 space-y-1">
                <template x-for="reason in reportReasons" :key="reason">
                    <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="global_report_reason" :value="reason"
                            x-model="selectedReason"
                            class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-700" x-text="reason"></span>
                    </label>
                </template>

                {{-- Description input --}}
                <div class="px-3 pt-2 pb-2">
                    <textarea x-model="customReason" rows="3" maxlength="500" placeholder="Vui lòng ghi rõ vấn đề..."
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-800 placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none"></textarea>
                    <p class="text-[11px] text-gray-400 mt-1 text-right" x-text="customReason.length + '/500'"></p>
                </div>

                {{-- Báo cáo công khai toggle --}}
                <div class="px-3 pb-2 pt-1">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative shrink-0">
                            <input type="checkbox" x-model="isPublicReport" class="sr-only">
                            {{-- Track --}}
                            <div class="w-10 h-6 rounded-full transition-colors duration-300"
                                 :class="isPublicReport ? 'bg-emerald-500' : 'bg-gray-300'"></div>
                            {{-- Thumb --}}
                            <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transition-transform duration-300"
                                 :class="isPublicReport ? 'translate-x-4' : 'translate-x-0'"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium transition-colors duration-200"
                                  :class="isPublicReport ? 'text-emerald-600' : 'text-gray-600'">
                                Báo cáo công khai
                            </span>
                            <p class="text-xs text-gray-400 leading-tight mt-0.5">Mọi người có thể thấy báo cáo của bạn</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-4 border-t border-gray-100 flex gap-2 justify-end">
                <button @click="reportModal = false; selectedReason = ''; customReason = ''; isPublicReport = false;"
                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    Hủy
                </button>
                <button @click="submitReport()"
                    :disabled="!selectedReason || submittingReport"
                    class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed min-w-[100px] text-center">
                    <span x-text="submittingReport ? 'Đang gửi...' : 'Gửi báo cáo'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Inline Toast Notification ────────────────────────────────── --}}
    <div x-show="toastShow"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         x-cloak
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl text-sm font-medium max-w-xs w-full mx-4 pointer-events-none"
         :class="toastSuccess
             ? 'bg-emerald-600 text-white'
             : 'bg-red-600 text-white'"
         style="display: none">

        {{-- Icon --}}
        <svg x-show="toastSuccess" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <svg x-show="!toastSuccess" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>

        <span x-text="toastMessage"></span>
    </div>

</div>
