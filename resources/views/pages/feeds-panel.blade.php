@foreach($matches as $match)
    <div class="feed-panel relative overflow-hidden">
        @if(str_starts_with($match->avatar, 'data:image/svg'))
            <div class="absolute inset-0 brand-gradient-diagonal">
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
                    <a href="{{ route('profile.show', ['user' => $match]) }}" class="hover:underline text-2xl font-bold text-white">
                        {{ $match->name }} <span class="ml-1 font-normal">, {{ $match->age }}</span>
                    </a>

                    @if($match->looking_for)
                        <p class="mt-1 text-sm text-white/80">{{ $match->looking_for->label() }}</p>
                    @endif

                    @if($match->interests->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach($match->interests->take(3) as $interest)
                                <x-badge variant="overlay">
                                    @if($interest->icon)
                                        <span>{{ $interest->icon }}</span>
                                    @endif
                                    {{ $interest->label }}
                                </x-badge>
                            @endforeach
                        </div>
                    @endif
                </div>

                <x-message-button :user="$match" class="bg-white/20 text-white backdrop-blur-sm hover:bg-white/30" />
            </div>
        </div>
    </div>
@endforeach

@if($matches->hasMorePages())
    <meta name="next-cursor" content="{{ $matches->nextCursor()->encode() }}">
@endif
