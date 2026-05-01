<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full py-3 px-4 bg-[#01b4e4] hover:bg-[#0090b8] text-white text-[15px] font-bold rounded-xl transition-colors flex items-center justify-center shadow-md focus:ring-4 focus:ring-[#01b4e4]/30 outline-none']) }}>
    {{ $slot }}
</button>
