@props([
    'icon' => 'face-smile',
    'title',
    'description' => '',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-4 py-16 text-center']) }}>
    <div class="mb-4 rounded-full bg-brand-50 p-4">
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-8 w-8 text-brand-400" />
    </div>
    <p class="text-sm font-medium text-gray-900">{{ $title }}</p>
    @if($description)
        <p class="mt-1 text-xs text-gray-500">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
