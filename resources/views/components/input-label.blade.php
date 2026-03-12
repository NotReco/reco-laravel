@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-dark-200']) }}>
    {{ $value ?? $slot }}
</label>
