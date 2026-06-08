@props(['type' => 'button', 'variant' => 'primary', 'size' => 'md'])

@php
    $sizeClass = match($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => ''
    };
@endphp

<button type="{{ $type }}" class="btn btn-{{ $variant }} {{ $sizeClass }}" {{ $attributes }}>
    {{ $slot }}
</button>
