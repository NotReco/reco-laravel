{{-- Expandable Text Component --}}
{{-- Usage: <x-expandable-text :text="$movie->synopsis" :maxLength="200" /> --}}

@props(['text', 'maxLength' => 200])

@if($text)
    @if(strlen($text) > $maxLength)
        <div x-data="{ expanded: false }" {{ $attributes }}>
            <p class="text-dark-300 text-sm leading-relaxed">
                <span x-show="!expanded">{{ Str::limit($text, $maxLength) }}</span>
                <span x-show="expanded" x-cloak>{{ $text }}</span>
            </p>
            <button @click="expanded = !expanded"
                class="text-rose-400 hover:text-rose-300 text-sm font-medium mt-1 transition-colors">
                <span x-text="expanded ? '← Thu gọn' : 'Xem thêm →'"></span>
            </button>
        </div>
    @else
        <p class="text-dark-300 text-sm leading-relaxed" {{ $attributes }}>{{ $text }}</p>
    @endif
@endif
