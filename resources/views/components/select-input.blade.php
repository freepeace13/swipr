@props([
    'name',
    'id' => null,
])

@php $id = $id ?? $name; @endphp

<select
    name="{{ $name }}"
    id="{{ $id }}"
    {{ $attributes->merge([
        'class' => 'mt-1 block w-full rounded-input border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200',
    ]) }}
>
    {{ $slot }}
</select>
