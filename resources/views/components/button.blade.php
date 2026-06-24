@props([
    'variant' => 'primary',
    'size' => 'md',
    'pill' => false,
    'as' => 'button',
    'href' => null,
])

@php
    $tag = $href ? 'a' : $as;

    $base = 'inline-flex items-center justify-center font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40';

    $variants = [
        'primary'   => 'bg-brand-600 text-white hover:bg-brand-700 focus:ring-brand-500 shadow-button',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-brand-500 shadow-button',
        'danger'    => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-button',
        'ghost'     => 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:ring-brand-500',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm gap-1.5',
        'md' => 'px-4 py-2 text-sm gap-2',
        'lg' => 'px-6 py-3 text-base gap-2',
    ];

    $radius = $pill ? 'rounded-full' : 'rounded-button';

    $classes = implode(' ', [
        $base,
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        $radius,
    ]);
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
    {{ $slot }}
</{{ $tag }}>
