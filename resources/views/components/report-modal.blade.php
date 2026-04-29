<div x-data="{
    reportModal: false,
    reportTargetType: '',
    reportTargetId: null,
    selectedReason: '',
    customReason: '',
    isPublicReport: false,
    submittingReport: false,
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
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            alert(data.success ? data.message : (data.message || 'Có lỗi xảy ra.'));
            
            this.reportModal = false;
            this.selectedReason = '';
            this.customReason = '';
            this.isPublicReport = false;
        } catch (err) {
            console.error('Error reporting:', err);
            alert('Không thể gửi báo cáo. Vui lòng thử lại.');
        } finally {
            this.submittingReport = false;
        }
    }
}" 
@open-report.window="openReport($event.detail.type, $event.detail.id)">
    <div x-show="reportModal" x-transition.opacity x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="reportModal = false" style="display: none">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden" @click.stop>
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Báo cáo bình luận</h3>
                <p class="text-xs text-gray-400 mt-0.5">Chọn lý do báo cáo</p>
            </div>
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

                {{-- Public Report toggle --}}
                <div class="px-3 pb-2 pt-1">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" x-model="isPublicReport" class="sr-only">
                            <div class="block bg-gray-200 w-10 h-6 rounded-full transition-colors duration-300" :class="{'bg-blue-500': isPublicReport}"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300" :class="{'translate-x-4': isPublicReport}"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Public Report (Báo cáo công khai)</span>
                    </label>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex gap-2 justify-end">
                <button @click="reportModal = false; selectedReason = ''; customReason = ''; isPublicReport = false;"
                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Hủy</button>
                <button @click="submitReport()"
                    :disabled="!selectedReason || submittingReport"
                    class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50">
                    <span x-text="submittingReport ? 'Đang gửi...' : 'Gửi báo cáo'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
