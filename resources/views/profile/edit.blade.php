<x-app-layout>
    <x-slot:title>Cài đặt Tài khoản</x-slot:title>

    <div class="py-12" x-data="unsavedChangesHub()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex items-center gap-3 mb-8">
                <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <h1 class="text-3xl font-display font-bold text-white">Cài đặt</h1>
            </div>

            <div class="card p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="card p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>

            <div class="card p-6 sm:p-8">
                @include('profile.partials.security-settings-form')
            </div>

            <div class="card p-6 sm:p-8 border-rose-900 border-2">
                @include('profile.partials.delete-user-form')
            </div>
            
        </div>

        <div
            x-cloak
            x-show="dirty"
            x-transition.opacity.duration.150ms
            class="fixed left-0 right-0 bottom-0 z-[9998] px-4 pb-4"
        >
            <div class="max-w-4xl mx-auto">
                <div class="rounded-2xl border border-dark-700 bg-dark-800/95 backdrop-blur shadow-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-amber-300">
                            Hãy cẩn thận - bạn chưa lưu các thay đổi!
                        </div>
                        <div class="text-xs text-dark-300 mt-0.5" x-show="dirtyTitles.length">
                            Khu vực: <span class="text-dark-200" x-text="dirtyTitles.join(', ')"></span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                        <button type="button" class="btn-ghost w-full sm:w-auto" x-on:click="resetAll()">
                            Đặt lại
                        </button>
                        <button type="button" class="btn-primary w-full sm:w-auto" x-on:click="save()">
                            Lưu thay đổi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('unsavedChangesHub', () => ({
                    dirtyFormIds: new Set(),
                    forms: [],
                    init() {
                        this.forms = Array.from(document.querySelectorAll('form[data-unsaved-bar]'));

                        for (const form of this.forms) {
                            form._unsavedInitial = this._snapshot(form);

                            const mark = () => this._recomputeDirty(form);
                            form.addEventListener('input', mark, { passive: true });
                            form.addEventListener('change', mark, { passive: true });
                            form.addEventListener('submit', () => this.dirtyFormIds.delete(form.id));
                        }

                        queueMicrotask(() => {
                            for (const form of this.forms) this._recomputeDirty(form);
                        });
                    },
                    get dirty() {
                        return this.dirtyFormIds.size > 0;
                    },
                    get dirtyTitles() {
                        const titles = [];
                        for (const form of this.forms) {
                            if (this.dirtyFormIds.has(form.id)) {
                                titles.push(form.dataset.unsavedTitle || form.id);
                            }
                        }
                        return titles;
                    },
                    _snapshot(form) {
                        const fd = new FormData(form);
                        const entries = [];
                        for (const [k, v] of fd.entries()) {
                            entries.push([k, v instanceof File ? v.name : String(v)]);
                        }
                        // include unchecked checkboxes so toggles are tracked reliably
                        for (const el of form.querySelectorAll('input[type="checkbox"][name]')) {
                            if (!el.checked) entries.push([el.name, '__unchecked__']);
                        }
                        entries.sort((x, y) => (x[0] + '=' + x[1]).localeCompare(y[0] + '=' + y[1]));
                        return JSON.stringify(entries);
                    },
                    _recomputeDirty(form) {
                        if (!form.id) return;
                        const now = this._snapshot(form);
                        const initial = form._unsavedInitial ?? now;
                        const isDirty = now !== initial;
                        if (isDirty) this.dirtyFormIds.add(form.id);
                        else this.dirtyFormIds.delete(form.id);
                    },
                    resetAll() {
                        for (const form of this.forms) {
                            if (this.dirtyFormIds.has(form.id)) {
                                form.reset();
                                this.dirtyFormIds.delete(form.id);
                            }
                        }
                    },
                    save() {
                        // submit the first dirty form (DOM order)
                        const target = this.forms.find(f => this.dirtyFormIds.has(f.id));
                        if (!target) return;
                        if (typeof target.requestSubmit === 'function') target.requestSubmit();
                        else target.submit();
                    },
                }));
            });
        </script>
    @endpush
</x-app-layout>
