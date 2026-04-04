<div id="autosave-container" class="hidden mb-6 p-4 bg-sky-500/10 border border-sky-500/20 rounded-xl flex items-center justify-between">
    <div class="text-sm text-sky-100/90">
        <span class="font-semibold text-sky-400">Có bản nháp chưa lưu!</span> 
        Hệ thống tìm thấy nội dung đang làm dở (lưu lúc <span id="autosave-time" class="font-medium text-white"></span>).
    </div>
    <div class="space-x-2 shrink-0 ml-4">
        <button type="button" id="btn-restore-draft" class="px-3 py-1.5 bg-sky-600 text-white text-xs font-semibold rounded-lg hover:bg-sky-700 active:scale-[0.97] transition-all">Khôi phục</button>
        <button type="button" id="btn-discard-draft" class="px-3 py-1.5 bg-dark-800 border border-dark-700 text-dark-300 text-xs font-medium rounded-lg hover:text-white hover:bg-dark-700 transition-colors">Bỏ qua</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('article-form');
    if (!form) return;

    // We place the autosave container right before the form
    const container = document.getElementById('autosave-container');
    form.parentNode.insertBefore(container, form);

    const storageKey = '{{ $autosaveKey }}';
    const timeSpan = document.getElementById('autosave-time');
    const btnRestore = document.getElementById('btn-restore-draft');
    const btnDiscard = document.getElementById('btn-discard-draft');
    const indicator = document.getElementById('autosave-indicator');

    // Display draft logic
    const draftJson = localStorage.getItem(storageKey);
    if (draftJson) {
        try {
            const draft = JSON.parse(draftJson);
            if (draft && draft.timestamp && draft.data) {
                const date = new Date(draft.timestamp);
                // Discard drafts older than 24 hours
                if (Date.now() - date.getTime() > 24 * 60 * 60 * 1000) {
                    localStorage.removeItem(storageKey);
                } else {
                    timeSpan.textContent = date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                    container.classList.remove('hidden');

                    btnRestore.addEventListener('click', () => {
                        restoreDraft(draft.data);
                        container.classList.add('hidden');
                        
                        if (indicator) {
                            indicator.textContent = 'Đã khôi phục bản nháp';
                            indicator.classList.remove('opacity-0');
                            setTimeout(() => indicator.classList.add('opacity-0'), 3000);
                        }
                    });

                    btnDiscard.addEventListener('click', () => {
                        localStorage.removeItem(storageKey);
                        container.classList.add('hidden');
                    });
                }
            }
        } catch (e) {
            localStorage.removeItem(storageKey);
        }
    }

    // Function to extract form data
    function getFormData() {
        const data = {};
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            if (input.name && input.type !== 'file' && input.type !== 'hidden' && input.name !== '_token' && input.name !== '_method') {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    data[input.name] = input.checked;
                } else {
                    data[input.name] = input.value;
                }
            }
        });
        if (window.tinymce && window.tinymce.get('content')) {
            data['content'] = window.tinymce.get('content').getContent();
        }
        return data;
    }

    // Function to restore form data
    function restoreDraft(data) {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            if (input.name && data[input.name] !== undefined && input.type !== 'file' && input.type !== 'hidden') {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = data[input.name];
                } else {
                    input.value = data[input.name];
                }
            }
        });
        if (window.tinymce && window.tinymce.get('content') && data['content'] !== undefined) {
            window.tinymce.get('content').setContent(data['content']);
        }
        
        // Dispatch input event to update any Alpine or JS listeners
        const firstInput = form.querySelector('input');
        if (firstInput) {
            firstInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    // Auto save periodically
    let lastSavedString = JSON.stringify(getFormData()); // Initial state
    setInterval(() => {
        const data = getFormData();
        
        // Ignore empty data or unmodified data
        if (!data.title && !data.content) return; // minimal requirement to save draft
        
        const dataString = JSON.stringify(data);
        if (dataString !== lastSavedString && Object.keys(data).length > 0) {
            const draft = {
                timestamp: Date.now(),
                data: data
            };
            localStorage.setItem(storageKey, JSON.stringify(draft));
            lastSavedString = dataString;
            
            // Show saving indicator
            if (indicator) {
                indicator.textContent = 'Đã lưu nháp ' + new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                indicator.classList.remove('opacity-0');
                
                // Hide container if they make changes and a draft was showing (assume they moved on)
                if (!container.classList.contains('hidden')) {
                    container.classList.add('hidden');
                }
            }
        }
    }, 10000); // 10 seconds checking interval

    // Clear draft on successful submit action
    // We attach an event listener to the form to handle standard submit
    form.addEventListener('submit', () => {
        // We sync TinyMCE content first just in case
        if (window.tinymce?.triggerSave) {
            window.tinymce.triggerSave();
        }
        // NOTE: We don't remove draft here because if the backend returns validation errors, 
        // the form will reload with errors but the user won't have the draft anymore if we clear it here.
        // That's why keeping the Draft in localStorage until they manually discard or override is better.
        // Actually, if validation fails, the Laravel old() will preserve data, so having a draft is redundant but harmless.
        
        // If we want to clear it upon success, the best way in Blade is to check for success session,
        // but it requires logic on the controller redirect page (index). 
        // For simplicity, we keep it as is. Discarding is manual or 24h expiration.
    });
});
</script>
