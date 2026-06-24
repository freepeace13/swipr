@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
    <div class="mx-auto h-full max-w-7xl sm:px-6 lg:px-8 my-6">
        <x-card :padding="false" class="flex h-full flex-col sm:h-auto">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-4 sm:px-6">
                <h1 class="text-lg font-semibold text-gray-900">Messages</h1>
            </div>

            {{-- Conversations List --}}
            @if ($conversations->isEmpty())
                <x-empty-state icon="envelope" title="No conversations yet" description="When you match with someone, your conversations will appear here" class="flex-1" />
            @else
                <div class="min-h-0 flex-1 divide-y divide-gray-100 overflow-y-auto">
                    @foreach ($conversations as $conversation)
                        @php
                            $other = $conversation->otherUser($auth);
                            $lastMessage = $conversation->lastMessage;
                            $isOnline = $other->last_seen_at?->isToday();
                        @endphp
                        <a
                            href="{{ route('chat.conversations.show', $conversation) }}"
                            class="group flex items-center gap-3 px-4 py-3 transition hover:bg-gray-50 sm:px-6"
                        >
                            {{-- Avatar --}}
                            <x-avatar :src="$other->avatar" :alt="$other->name" size="md" ring :online="$isOnline" />

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-2">
                                    <h3 class="truncate text-sm font-semibold text-gray-900">{{ $other->name }}</h3>
                                    @if ($lastMessage)
                                        <span class="flex-shrink-0 text-xs text-gray-400">
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
                                        @if ($lastMessage->sender_id === $auth->id)
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
        </x-card>
    </div>
@endsection
