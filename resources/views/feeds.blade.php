@extends('layouts.app')

@section('title', 'Discover')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            @if($matches->isEmpty())
                <div class="rounded-2xl bg-white p-12 text-center shadow">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">No matches yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Try broadening your preferences to discover more people.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($matches as $match)
                        <a href="{{ route('profile', $match) }}" class="group block overflow-hidden rounded-2xl bg-white shadow transition hover:shadow-lg">
                            <div class="relative">
                                <img
                                    src="{{ $match->avatar }}"
                                    alt="{{ $match->name }}"
                                    class="aspect-[3/4] w-full object-cover transition group-hover:scale-105"
                                >
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent px-4 pb-4 pt-16">
                                    <h3 class="text-lg font-bold text-white">
                                        {{ $match->name }}<span class="ml-1 font-normal">, {{ $match->age }}</span>
                                    </h3>
                                    @if($match->looking_for)
                                        <p class="mt-0.5 text-sm text-white/80">{{ $match->looking_for->label() }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($match->interests->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 px-4 py-3">
                                    @foreach($match->interests->take(4) as $interest)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                                            @if($interest->icon)
                                                <span>{{ $interest->icon }}</span>
                                            @endif
                                            {{ $interest->label }}
                                        </span>
                                    @endforeach
                                    @if($match->interests->count() > 4)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                            +{{ $match->interests->count() - 4 }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
@endsection
