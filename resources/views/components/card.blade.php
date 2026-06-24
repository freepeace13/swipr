@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-card bg-white shadow-card' . ($padding ? ' p-6' : '')]) }}>
    {{ $slot }}
</div>
