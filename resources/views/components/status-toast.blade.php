{{-- Status Toast — permanent, bottom-left, dismiss on click only --}}
@php
    $status = session('status');
    // Map known Laravel status keys to Vietnamese messages
    $messages = [
        'verification-link-sent' => 'Liên kết xác minh mới đã được gửi đến email của bạn.',
        'passwords.sent'         => 'Chúng tôi đã gửi liên kết đặt lại mật khẩu đến email của bạn.',
        'passwords.reset'        => 'Mật khẩu của bạn đã được đặt lại thành công.',
        'passwords.throttled'    => 'Vui lòng chờ trước khi thử lại.',
    ];
    $message = $messages[$status] ?? $status;
@endphp

@if($status)
<div x-data="{ show: true }"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-6 right-6 z-50 max-w-sm w-full"
>
    <div class="flex items-start gap-3 px-5 py-4 bg-white border border-green-200 rounded-xl shadow-lg">
        <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm text-gray-700 font-medium leading-relaxed">{{ $message }}</p>
        <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600 transition-colors shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif
