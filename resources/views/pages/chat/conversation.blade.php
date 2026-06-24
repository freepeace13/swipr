@extends('layouts.app')

@section('title', 'Conversation')

@section('mainClass', 'overflow-hidden')

@section('content')
    @php
        $me = auth()->user();
        $other = $conversation->sender_id === $me->id
            ? $conversation->recipient
            : $conversation->sender;
        $messages = $conversation->messages->sortBy('created_at');
    @endphp
    <div class="mx-auto flex h-full sm:px-6 lg:px-8 max-w-7xl flex-col">
        {{-- Chat Header --}}
        <div class="flex items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 sm:px-6">
            <a href="{{ route('chat.inbox') }}" class="mr-1 flex-shrink-0 rounded-full p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 sm:hidden">
                <x-heroicon-o-chevron-left class="h-5 w-5" />
            </a>

            <a href="{{ route('profile.show', $other) }}" class="flex-shrink-0">
                <img
                    src="{{ $other->avatar }}"
                    alt="{{ $other->name }}"
                    class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-100"
                >
            </a>

            <div class="min-w-0 flex-1">
                <a href="{{ route('profile.show', $other) }}" class="block truncate text-sfom nt-semibold text-gray-900 hover:underline">
                    {{ $other->name }}
                </a>
                @if ($other->last_seen_at?->isToday())
                    <p class="text-xs text-green-600">Online</p>
                @elseif ($other->last_seen_at)
                    <p class="text-xs text-gray-500">Last seen {{ $other->last_seen_at->diffForHumans() }}</p>
                @endif
            </div>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600">
                    <x-heroicon-m-ellipsis-vertical class="h-5 w-5" />
                </button>
                <div
                    x-show="open"
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 top-full z-10 mt-1 w-48 rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5"
                >
                    <a href="{{ route('profile.show', $other) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">View Profile</a>
                    <form method="POST" action="{{ route('chat.conversations.destroy', $conversation) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete Conversation</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Messages Area --}}
        <div
            id="messages-container"
            class="flex-1 overflow-y-auto bg-gray-50 px-4 py-6 sm:px-6"
            x-data
            x-init="$el.scrollTop = $el.scrollHeight"
        >
            @if ($messages->isEmpty())
                <div class="flex h-full flex-col items-center justify-center text-center">
                    <div class="mb-4 rounded-full bg-indigo-50 p-4">
                        <x-heroicon-o-chat-bubble-oval-left-ellipsis class="h-8 w-8 text-indigo-400" />
                    </div>
                    <p class="text-sm font-medium text-gray-900">No messages yet</p>
                    <p class="mt-1 text-xs text-gray-500">Send a message to start the conversation</p>
                </div>
            @else
                <div class="space-y-1">
                    @php $lastDate = null; @endphp
                    @foreach ($messages as $message)
                        @php
                            $isMine = $message->sender_id === $me->id;
                            $messageDate = $message->created_at->toDateString();
                            $showDate = $messageDate !== $lastDate;
                            $lastDate = $messageDate;
                        @endphp

                        {{-- Date Separator --}}
                        @if ($showDate)
                            <div class="flex items-center justify-center py-3">
                                <span class="rounded-full bg-gray-200/80 px-3 py-1 text-xs font-medium text-gray-600">
                                    {{ $message->created_at->isToday() ? 'Today' : ($message->created_at->isYesterday() ? 'Yesterday' : $message->created_at->format('M j, Y')) }}
                                </span>
                            </div>
                        @endif

                        {{-- Message Bubble --}}
                        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                            <div class="group relative max-w-[75%] sm:max-w-[65%]">
                                <div class="{{ $isMine ? 'bg-indigo-600 text-white rounded-2xl rounded-br-md' : 'bg-white text-gray-900 rounded-2xl rounded-bl-md shadow-sm ring-1 ring-gray-100' }} px-4 py-2.5">
                                    @if ($message->type === \App\Enums\Chat\MessageType::Image && $message->attachments)
                                        <div class="mb-1.5 overflow-hidden rounded-lg">
                                            @foreach ($message->attachments as $attachment)
                                                <img src="{{ $attachment['url'] }}" alt="{{ $attachment['name'] ?? 'Image' }}" class="max-h-64 w-full object-cover">
                                            @endforeach
                                        </div>
                                    @endif

                                    @if ($message->type === \App\Enums\Chat\MessageType::File && $message->attachments)
                                        @foreach ($message->attachments as $attachment)
                                            <a href="{{ $attachment['url'] }}" target="_blank" class="mb-1.5 flex items-center gap-2 rounded-lg {{ $isMine ? 'bg-indigo-700/50' : 'bg-gray-50' }} px-3 py-2">
                                                <x-heroicon-o-document class="h-5 w-5 flex-shrink-0 {{ $isMine ? 'text-indigo-200' : 'text-gray-400' }}" />
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-medium {{ $isMine ? 'text-white' : 'text-gray-900' }}">{{ $attachment['name'] ?? 'File' }}</p>
                                                    @if (isset($attachment['size']))
                                                        <p class="text-xs {{ $isMine ? 'text-indigo-200' : 'text-gray-500' }}">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    @endif

                                    @if ($message->body)
                                        <p class="whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $message->body }}</p>
                                    @endif
                                </div>

                                {{-- Timestamp --}}
                                <div class="mt-0.5 flex items-center gap-1 px-1 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                    <span class="text-[11px] text-gray-400">{{ $message->created_at->format('g:i A') }}</span>
                                    @if ($isMine)
                                        @php
                                            $status = $message->status->first();
                                        @endphp
                                        @if ($status?->read_at)
                                            <x-heroicon-m-check class="h-3.5 w-3.5 text-indigo-500" />
                                        @elseif ($status?->delivered_at)
                                            <x-heroicon-m-check class="h-3.5 w-3.5 text-gray-400" />
                                        @else
                                            <x-heroicon-o-clock class="h-3.5 w-3.5 text-gray-300" />
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Message Input --}}
        <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <form
                class="flex items-end gap-2"
                x-data="{
                    body: '',
                    rows: 1,
                    sending: false,
                    async send() {
                        if (!this.body.trim() || this.sending) return;
                        this.sending = true;
                        try {
                            const res = await fetch('{{ route('chat.conversations.messages.store', $conversation) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ body: this.body }),
                            });
                            if (!res.ok) throw await res.json();
                            const data = await res.json();
                            this.appendMessage(data.message);
                            this.body = '';
                            this.rows = 1;
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.sending = false;
                            this.$nextTick(() => this.$refs.input.focus());
                        }
                    },
                    appendMessage(msg) {
                        const container = document.getElementById('messages-container');
                        const list = container.querySelector('.space-y-1') || (() => {
                            const empty = container.querySelector('.flex.h-full');
                            if (empty) empty.remove();
                            const div = document.createElement('div');
                            div.className = 'space-y-1';
                            container.appendChild(div);
                            return div;
                        })();

                        const tpl = document.getElementById('sent-message-template');
                        const clone = tpl.content.cloneNode(true);

                        const time = new Date(msg.created_at);
                        clone.querySelector('[data-body]').textContent = msg.body;
                        clone.querySelector('[data-time]').textContent = time.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });

                        list.appendChild(clone);
                        container.scrollTop = container.scrollHeight;
                    },
                }"
                @submit.prevent="send()"
            >
                <div class="min-w-0 flex-1">
                    <textarea
                        x-ref="input"
                        x-model="body"
                        x-on:input="rows = Math.min(body.split('\n').length, 5)"
                        x-on:keydown.enter.prevent="if (!$event.shiftKey) send()"
                        :rows="rows"
                        :disabled="sending"
                        placeholder="Type a message..."
                        class="block w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    :disabled="!body.trim() || sending"
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40"
                >
                    <x-heroicon-o-paper-airplane class="h-5 w-5" />
                </button>
            </form>
        </div>
    </div>

    <template id="sent-message-template">
        <div class="flex justify-end">
            <div class="group relative max-w-[75%] sm:max-w-[65%]">
                <div class="bg-indigo-600 text-white rounded-2xl rounded-br-md px-4 py-2.5">
                    <p data-body class="whitespace-pre-wrap break-words text-sm leading-relaxed"></p>
                </div>
                <div class="mt-0.5 flex items-center gap-1 px-1 justify-end">
                    <span data-time class="text-[11px] text-gray-400"></span>
                    <svg class="h-3.5 w-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('bodyClass', '')
