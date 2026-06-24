@foreach($matches as $match)
    <div class="feed-panel relative overflow-hidden">
        @if(str_starts_with($match->avatar, 'data:image/svg'))
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600">
                <img
                    src="{{ $match->avatar }}"
                    alt="{{ $match->name }}"
                    class="absolute left-1/2 top-1/3 h-32 w-32 -translate-x-1/2 -translate-y-1/2 rounded-full border-4 border-white/30 shadow-lg"
                >
            </div>
        @else
            <img
                src="{{ $match->avatar }}"
                alt="{{ $match->name }}"
                class="absolute inset-0 h-full w-full object-cover"
                loading="lazy"
            >
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

        <div class="absolute inset-x-0 bottom-0 pb-8">
            <div class="mx-auto flex max-w-7xl items-end justify-between px-4 sm:px-6 lg:px-8">
                <div>
                    <h2 class="text-2xl font-bold text-white">
                        {{ $match->name }}<span class="ml-1 font-normal">, {{ $match->age }}</span>
                    </h2>

                    @if($match->looking_for)
                        <p class="mt-1 text-sm text-white/80">{{ $match->looking_for->label() }}</p>
                    @endif

                    @if($match->interests->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach($match->interests->take(3) as $interest)
                                <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-medium text-white backdrop-blur-sm">
                                    @if($interest->icon)
                                        <span>{{ $interest->icon }}</span>
                                    @endif
                                    {{ $interest->label }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <a
                    href="{{ route('profile.show', $match) }}"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/20 text-white backdrop-blur-sm transition hover:bg-white/30"
                    title="Message {{ $match->name }}"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endforeach

@if($matches->hasMorePages())
    <meta name="next-cursor" content="{{ $matches->nextCursor()->encode() }}">
@endif
