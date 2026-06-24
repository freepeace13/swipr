@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
    @php $me = auth()->user(); @endphp

    <div class="mx-auto h-full max-w-7xl sm:px-6 lg:px-8">
        <div class="flex h-full flex-col bg-white sm:h-auto sm:shadow">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-4 sm:px-6">
                <h1 class="text-lg font-semibold text-gray-900">Messages</h1>
            </div>

            {{-- Conversations List --}}
            @if ($conversations->isEmpty())
                <div class="flex flex-1 flex-col items-center justify-center px-4 py-16 text-center">
                    <div class="mb-4 rounded-full bg-indigo-50 p-4">
                        <x-heroicon-o-envelope class="h-8 w-8 text-indigo-400" />
                    </div>
                    <p class="text-sm font-medium text-gray-900">No conversations yet</p>
                    <p class="mt-1 text-xs text-gray-500">When you match with someone, your conversations will appear here</p>
                </div>
            @else
                <div class="min-h-0 flex-1 divide-y divide-gray-100 overflow-y-auto">
                    @foreach ($conversations as $conversation)
                        @php
                            $other = $conversation->otherUser($me);
                            $lastMessage = $conversation->lastMessage;
                            $isOnline = $other->last_seen_at?->isToday();
                        @endphp
                        <a
                            href="{{ route('chat.conversations.show', $conversation) }}"
                            class="group flex items-center gap-3 px-4 py-3 transition hover:bg-gray-50 sm:px-6"
                        >
                            {{-- Avatar --}}
                            <div class="relative flex-shrink-0">
                                <img
                                    src="{{ $other->avatar }}"
                                    alt="{{ $other->name }}"
                                    class="h-12 w-12 rounded-full object-cover ring-2 ring-gray-100"
                                >
                                @if ($isOnline)
                                    <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-green-500"></span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-2">
                                    <h3 class="truncate text-sm font-semibold text-gray-900">{{ $other->name }}</h3>
                                    @if ($lastMessage)
                                        <span class="flex-shrink-0 text-[11px] text-gray-400">
                                            @if ($lastMessage->created_at->isToday())
                                                {{ $lastMessage->created_at->format('g:i A') }}
                                            @elseif ($lastMessage->created_at->isYesterday())
                                                Yesterday
                                            @elseif ($lastMessage->created_at->isCurrentYear())
                                                {{ $lastMessage->created_at->format('M j') }}
                                            @else
                                                {{ $lastMessage->created_at->format('M j, Y') }}
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                @if ($lastMessage)
                                    <p class="mt-0.5 truncate text-sm text-gray-500">
                                        @if ($lastMessage->sender_id === $me->id)
                                            <span class="text-gray-400">You: </span>
                                        @endif
                                        @if ($lastMessage->type !== \App\Enums\Chat\MessageType::Text)
                                            @if ($lastMessage->type === \App\Enums\Chat\MessageType::Image)
                                                <x-heroicon-o-photo class="mb-0.5 inline h-3.5 w-3.5" />
                                                Photo
                                            @elseif ($lastMessage->type === \App\Enums\Chat\MessageType::File)
                                                <x-heroicon-o-document class="mb-0.5 inline h-3.5 w-3.5" />
                                                File
                                            @endif
                                        @else
                                            {{ $lastMessage->body }}
                                        @endif
                                    </p>
                                @else
                                    <p class="mt-0.5 text-sm italic text-gray-400">No messages yet</p>
                                @endif
                            </div>

                            {{-- Chevron --}}
                            <x-heroicon-o-chevron-right class="h-4 w-4 flex-shrink-0 text-gray-300 transition group-hover:text-gray-400" />
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($conversations->hasPages())
                    <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
                        {{ $conversations->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
