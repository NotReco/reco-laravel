@props([
    'name',
    'value'    => '',
    'type'     => 'country',  // 'country' | 'language'
    'label'    => null,
    'id'       => null,
])

@php
$countries = [
    ['code' => 'US', 'name' => 'Hoa Kỳ', 'en' => 'United States'],
    ['code' => 'GB', 'name' => 'Anh', 'en' => 'United Kingdom'],
    ['code' => 'JP', 'name' => 'Nhật Bản', 'en' => 'Japan'],
    ['code' => 'KR', 'name' => 'Hàn Quốc', 'en' => 'South Korea'],
    ['code' => 'CN', 'name' => 'Trung Quốc', 'en' => 'China'],
    ['code' => 'FR', 'name' => 'Pháp', 'en' => 'France'],
    ['code' => 'DE', 'name' => 'Đức', 'en' => 'Germany'],
    ['code' => 'IT', 'name' => 'Ý', 'en' => 'Italy'],
    ['code' => 'ES', 'name' => 'Tây Ban Nha', 'en' => 'Spain'],
    ['code' => 'IN', 'name' => 'Ấn Độ', 'en' => 'India'],
    ['code' => 'AU', 'name' => 'Úc', 'en' => 'Australia'],
    ['code' => 'CA', 'name' => 'Canada', 'en' => 'Canada'],
    ['code' => 'RU', 'name' => 'Nga', 'en' => 'Russia'],
    ['code' => 'BR', 'name' => 'Brazil', 'en' => 'Brazil'],
    ['code' => 'MX', 'name' => 'Mexico', 'en' => 'Mexico'],
    ['code' => 'TH', 'name' => 'Thái Lan', 'en' => 'Thailand'],
    ['code' => 'VN', 'name' => 'Việt Nam', 'en' => 'Vietnam'],
    ['code' => 'TW', 'name' => 'Đài Loan', 'en' => 'Taiwan'],
    ['code' => 'HK', 'name' => 'Hồng Kông', 'en' => 'Hong Kong'],
    ['code' => 'SG', 'name' => 'Singapore', 'en' => 'Singapore'],
    ['code' => 'MY', 'name' => 'Malaysia', 'en' => 'Malaysia'],
    ['code' => 'PH', 'name' => 'Philippines', 'en' => 'Philippines'],
    ['code' => 'ID', 'name' => 'Indonesia', 'en' => 'Indonesia'],
    ['code' => 'PK', 'name' => 'Pakistan', 'en' => 'Pakistan'],
    ['code' => 'TR', 'name' => 'Thổ Nhĩ Kỳ', 'en' => 'Turkey'],
    ['code' => 'SA', 'name' => 'Ả Rập Saudi', 'en' => 'Saudi Arabia'],
    ['code' => 'SE', 'name' => 'Thụy Điển', 'en' => 'Sweden'],
    ['code' => 'NO', 'name' => 'Na Uy', 'en' => 'Norway'],
    ['code' => 'DK', 'name' => 'Đan Mạch', 'en' => 'Denmark'],
    ['code' => 'FI', 'name' => 'Phần Lan', 'en' => 'Finland'],
    ['code' => 'NL', 'name' => 'Hà Lan', 'en' => 'Netherlands'],
    ['code' => 'BE', 'name' => 'Bỉ', 'en' => 'Belgium'],
    ['code' => 'CH', 'name' => 'Thụy Sĩ', 'en' => 'Switzerland'],
    ['code' => 'AT', 'name' => 'Áo', 'en' => 'Austria'],
    ['code' => 'PL', 'name' => 'Ba Lan', 'en' => 'Poland'],
    ['code' => 'CZ', 'name' => 'Séc', 'en' => 'Czech Republic'],
    ['code' => 'PT', 'name' => 'Bồ Đào Nha', 'en' => 'Portugal'],
    ['code' => 'GR', 'name' => 'Hy Lạp', 'en' => 'Greece'],
    ['code' => 'AR', 'name' => 'Argentina', 'en' => 'Argentina'],
    ['code' => 'CL', 'name' => 'Chile', 'en' => 'Chile'],
    ['code' => 'CO', 'name' => 'Colombia', 'en' => 'Colombia'],
    ['code' => 'ZA', 'name' => 'Nam Phi', 'en' => 'South Africa'],
    ['code' => 'NG', 'name' => 'Nigeria', 'en' => 'Nigeria'],
    ['code' => 'EG', 'name' => 'Ai Cập', 'en' => 'Egypt'],
    ['code' => 'IL', 'name' => 'Israel', 'en' => 'Israel'],
    ['code' => 'IR', 'name' => 'Iran', 'en' => 'Iran'],
    ['code' => 'NZ', 'name' => 'New Zealand', 'en' => 'New Zealand'],
    ['code' => 'HU', 'name' => 'Hungary', 'en' => 'Hungary'],
    ['code' => 'RO', 'name' => 'Romania', 'en' => 'Romania'],
    ['code' => 'UA', 'name' => 'Ukraine', 'en' => 'Ukraine'],
];

$languages = [
    ['code' => 'en', 'name' => 'Tiếng Anh', 'en' => 'English'],
    ['code' => 'ja', 'name' => 'Tiếng Nhật', 'en' => 'Japanese'],
    ['code' => 'ko', 'name' => 'Tiếng Hàn', 'en' => 'Korean'],
    ['code' => 'zh', 'name' => 'Tiếng Trung', 'en' => 'Chinese'],
    ['code' => 'fr', 'name' => 'Tiếng Pháp', 'en' => 'French'],
    ['code' => 'de', 'name' => 'Tiếng Đức', 'en' => 'German'],
    ['code' => 'es', 'name' => 'Tiếng Tây Ban Nha', 'en' => 'Spanish'],
    ['code' => 'it', 'name' => 'Tiếng Ý', 'en' => 'Italian'],
    ['code' => 'pt', 'name' => 'Tiếng Bồ Đào Nha', 'en' => 'Portuguese'],
    ['code' => 'ru', 'name' => 'Tiếng Nga', 'en' => 'Russian'],
    ['code' => 'hi', 'name' => 'Tiếng Hindi', 'en' => 'Hindi'],
    ['code' => 'ar', 'name' => 'Tiếng Ả Rập', 'en' => 'Arabic'],
    ['code' => 'th', 'name' => 'Tiếng Thái', 'en' => 'Thai'],
    ['code' => 'vi', 'name' => 'Tiếng Việt', 'en' => 'Vietnamese'],
    ['code' => 'id', 'name' => 'Tiếng Indonesia', 'en' => 'Indonesian'],
    ['code' => 'ms', 'name' => 'Tiếng Mã Lai', 'en' => 'Malay'],
    ['code' => 'tr', 'name' => 'Tiếng Thổ Nhĩ Kỳ', 'en' => 'Turkish'],
    ['code' => 'pl', 'name' => 'Tiếng Ba Lan', 'en' => 'Polish'],
    ['code' => 'nl', 'name' => 'Tiếng Hà Lan', 'en' => 'Dutch'],
    ['code' => 'sv', 'name' => 'Tiếng Thụy Điển', 'en' => 'Swedish'],
    ['code' => 'da', 'name' => 'Tiếng Đan Mạch', 'en' => 'Danish'],
    ['code' => 'fi', 'name' => 'Tiếng Phần Lan', 'en' => 'Finnish'],
    ['code' => 'no', 'name' => 'Tiếng Na Uy', 'en' => 'Norwegian'],
    ['code' => 'cs', 'name' => 'Tiếng Séc', 'en' => 'Czech'],
    ['code' => 'hu', 'name' => 'Tiếng Hungary', 'en' => 'Hungarian'],
    ['code' => 'ro', 'name' => 'Tiếng Romania', 'en' => 'Romanian'],
    ['code' => 'uk', 'name' => 'Tiếng Ukraine', 'en' => 'Ukrainian'],
    ['code' => 'el', 'name' => 'Tiếng Hy Lạp', 'en' => 'Greek'],
    ['code' => 'he', 'name' => 'Tiếng Do Thái', 'en' => 'Hebrew'],
    ['code' => 'fa', 'name' => 'Tiếng Ba Tư', 'en' => 'Persian'],
    ['code' => 'tl', 'name' => 'Tiếng Philippines', 'en' => 'Filipino'],
    ['code' => 'cn', 'name' => 'Tiếng Quảng Đông', 'en' => 'Cantonese'],
    ['code' => 'ta', 'name' => 'Tiếng Tamil', 'en' => 'Tamil'],
];

$tvTypes = [
    ['code' => 'Scripted', 'name' => 'Có kịch bản', 'en' => 'Scripted'],
    ['code' => 'Reality', 'name' => 'Truyền hình thực tế', 'en' => 'Reality'],
    ['code' => 'Documentary', 'name' => 'Tài liệu', 'en' => 'Documentary'],
    ['code' => 'Miniseries', 'name' => 'Mini-series', 'en' => 'Miniseries'],
    ['code' => 'News', 'name' => 'Tin tức', 'en' => 'News'],
    ['code' => 'Talk Show', 'name' => 'Trò chuyện truyền hình', 'en' => 'Talk Show'],
    ['code' => 'Video', 'name' => 'Video', 'en' => 'Video'],
];

$items = match($type) {
    'language' => $languages,
    'tv_type' => $tvTypes,
    default => $countries,
};
$fieldId = $id ?? ($name . '_picker');

if (!isset($label)) {
    $label = match($type) {
        'language' => 'Ngôn ngữ gốc',
        'tv_type' => 'Loại',
        default => 'Quốc gia',
    };
}

$placeholderType = match($type) {
    'language' => 'ngôn ngữ',
    'tv_type' => 'loại',
    default => 'quốc gia',
};

// Tìm label hiện tại từ value
$currentItem = collect($items)->firstWhere('code', $value);
$displayLabel = $currentItem 
    ? ($type === 'tv_type' ? $currentItem['name'] : "{$currentItem['code']} — {$currentItem['name']}") 
    : '';
@endphp

<div
    x-data="{
        open: false,
        search: '',
        value: '{{ old($name, $value) }}',
        display: '{{ $displayLabel }}',
        items: {{ Js::from($items) }},
        get filtered() {
            if (!this.search) return this.items;
            const q = this.search.toLowerCase();
            return this.items.filter(i =>
                i.code.toLowerCase().includes(q) ||
                i.name.toLowerCase().includes(q) ||
                i.en.toLowerCase().includes(q)
            );
        },
        select(item) {
            this.value   = item.code;
            this.display = {{ $type === 'tv_type' ? 'true' : 'false' }} ? item.name : (item.code + ' — ' + item.name);
            this.search  = '';
            this.open    = false;
        },
        clear() {
            this.value   = '';
            this.display = '';
            this.search  = '';
        }
    }"
    x-on:keydown.escape="open = false"
    class="relative"
>
    <label class="block text-sm font-medium text-dark-200 mb-1.5">{{ $label }}</label>

    {{-- Hidden real input --}}
    <input type="hidden" name="{{ $name }}" :value="value">

    {{-- Display trigger --}}
    <div
        @click="open = !open"
        class="input-dark text-sm flex items-center justify-between cursor-pointer select-none"
        :class="open ? 'border-sky-500/60 ring-1 ring-sky-500/30' : ''"
    >
        <span :class="display ? 'text-dark-100' : 'text-dark-500'" x-text="display || 'Chọn {{ $placeholderType }}...'"></span>
        <div class="flex items-center gap-1.5 shrink-0">
            <button
                type="button"
                x-show="value"
                @click.stop="clear()"
                class="text-dark-500 hover:text-dark-300 transition-colors"
                title="Xóa"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <svg class="w-4 h-4 text-dark-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.outside="open = false"
        class="absolute z-[100] w-full bottom-full mb-1 bg-dark-900 border border-dark-700 rounded-xl shadow-2xl shadow-black/50 overflow-hidden"
    >
        {{-- Search box --}}
        <div class="p-2 border-b border-dark-800">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-dark-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input
                    type="text"
                    x-model="search"
                    x-ref="searchInput"
                    x-on:keydown.enter.prevent="if(filtered.length) select(filtered[0])"
                    @click.stop
                    placeholder="Tìm kiếm..."
                    class="w-full bg-dark-800 border border-dark-700 rounded-lg text-sm text-dark-100 pl-8 pr-3 py-1.5 focus:outline-none focus:border-sky-500/60 placeholder-dark-500"
                    autocomplete="off"
                >
            </div>
        </div>

        {{-- Options list --}}
        <ul class="max-h-52 overflow-y-auto py-1">
            <template x-for="item in filtered" :key="item.code">
                <li
                    @click="select(item)"
                    class="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-dark-800 transition-colors"
                    :class="value === item.code ? 'bg-sky-600/15' : ''"
                >
                    @if ($type !== 'tv_type')
                        <span class="text-xs font-mono font-semibold text-sky-400 w-7 shrink-0" x-text="item.code"></span>
                    @endif
                    <span class="text-sm text-dark-200" x-text="item.name"></span>
                    <span class="text-xs text-dark-500 ml-auto" x-text="item.en"></span>
                    <svg x-show="value === item.code" class="w-3.5 h-3.5 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-3 py-4 text-sm text-dark-500 text-center">
                Không tìm thấy kết quả
            </li>
        </ul>
    </div>
</div>
