<x-guest-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h2 class="text-2xl font-display font-bold text-white">Xác thực hai lớp</h2>
            <p class="mt-2 text-dark-400">Mã xác thực 6 số đã được gửi đến email của bạn</p>
        </div>

        {{-- Status --}}
        @if (session('status'))
            <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-5">
            @csrf

            {{-- Code Input --}}
            <div>
                <label for="code" class="block text-sm font-medium text-dark-200 mb-2">Mã xác thực</label>
                <input id="code" type="text" name="code"
                    class="input-dark text-center text-2xl tracking-[0.5em] font-mono" placeholder="000000"
                    maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required autofocus
                    autocomplete="one-time-code">
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            {{-- Info --}}
            <div class="flex items-start gap-3 p-4 rounded-xl bg-dark-800/50 border border-dark-700/50">
                <svg class="w-5 h-5 text-accent-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <p class="text-sm text-dark-400">
                    Kiểm tra hộp thư email của bạn. Mã có hiệu lực trong <strong class="text-dark-200">10 phút</strong>.
                </p>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Xác thực
            </button>
        </form>

        {{-- Resend --}}
        <div class="flex items-center justify-between">
            <form method="POST" action="{{ route('2fa.resend') }}">
                @csrf
                <button type="submit" class="text-sm text-accent-400 hover:text-accent-300 transition-colors">
                    Gửi lại mã
                </button>
            </form>

            <a href="{{ route('login') }}" class="text-sm text-dark-400 hover:text-dark-300 transition-colors">
                ← Quay lại đăng nhập
            </a>
        </div>
    </div>
</x-guest-layout>