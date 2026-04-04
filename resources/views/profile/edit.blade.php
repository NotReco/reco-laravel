<x-app-layout>
    <x-slot:title>Hồ sơ</x-slot:title>

    <div class="py-12" x-data="unsavedChangesHub()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex items-center gap-3 mb-8">
                <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h1 class="text-3xl font-display font-bold text-white">Hồ sơ</h1>
            </div>

            <div class="card p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="card p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>

            <div class="card p-6 sm:p-8 border-sky-900 border-2">
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
                <div class="rounded-2xl border border-gray-200 bg-white shadow-xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-900">
                            Hãy cẩn thận - bạn chưa lưu các thay đổi!
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                        <button
                            type="button"
                            class="w-full sm:w-auto px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 underline-offset-4 hover:underline"
                            x-on:click="resetAll()"
                        >
                            Đặt lại
                        </button>
                        <button
                            type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white transition"
                            x-on:click="save()"
                        >
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
                    _snapshot(form) {
                        const fd = new FormData(form);
                        const entries = [];
                        for (const [k, v] of fd.entries()) {
                            entries.push([k, v instanceof File ? v.name : String(v)]);
                        }
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
