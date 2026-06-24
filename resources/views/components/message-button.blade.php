@props([
    'user',
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'h-10 w-10 [&>svg]:h-5 [&>svg]:w-5',
        'md' => 'h-12 w-12 [&>svg]:h-6 [&>svg]:w-6',
    ];
@endphp

<form method="POST" action="{{ route('chat.conversations.store') }}">
    @csrf
    <input type="hidden" name="recipient_id" value="{{ $user->id }}">
    <button
        type="submit"
        {{ $attributes->merge(['class' => 'flex shrink-0 items-center justify-center rounded-full transition ' . ($sizes[$size] ?? $sizes['md'])]) }}
        title="Message {{ $user->name }}"
    >
        <x-heroicon-o-chat-bubble-oval-left-ellipsis />
    </button>
</form>
