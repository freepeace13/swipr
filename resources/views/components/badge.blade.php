@props([
    'variant' => 'default',
])

@php
    $variants = [
        'default' => 'rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-700',
        'brand'   => 'rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700',
        'overlay' => 'rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-medium text-white backdrop-blur-sm',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 ' . ($variants[$variant] ?? $variants['default'])]) }}>
    {{ $slot }}
</span>
