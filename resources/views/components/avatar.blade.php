@props([
    'src',
    'alt' => '',
    'size' => 'md',
    'ring' => false,
    'online' => false,
])

@php
    $sizes = [
        'sm'  => 'h-10 w-10',
        'md'  => 'h-12 w-12',
        'lg'  => 'h-16 w-16',
        'xl'  => 'h-28 w-28',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $ringClass = $ring ? 'ring-2 ring-gray-100' : '';
@endphp

<div class="relative inline-flex flex-shrink-0">
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        {{ $attributes->merge(['class' => "$sizeClass rounded-full object-cover $ringClass"]) }}
    >
    @if($online)
        <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-green-500"></span>
    @endif
</div>
