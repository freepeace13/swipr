@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Header card: avatar + name + basics --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow">
                <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                <div class="relative px-6 pb-6">
                    <div class="-mt-16 flex items-end gap-5">
                        <img
                            src="{{ $user->avatar }}"
                            alt="{{ $user->name }}"
                            class="h-28 w-28 rounded-full border-4 border-white bg-gray-200 object-cover shadow-md"
                        >
                        <div class="pb-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}<span class="ml-1 text-lg font-normal text-gray-500">, {{ $user->age }}</span></h1>
                            @if($user->gender)
                                <p class="mt-0.5 text-sm text-gray-500">{{ $user->gender->label() }}</p>
                            @endif
                        </div>

                        @can('edit', $user)
                            <a href="{{ route('settings', ['tab' => 'profile']) }}"
                               class="ml-auto mb-1 inline-flex items-center gap-1.5 rounded-md bg-gray-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-700">
                                <x-heroicon-m-pencil-square class="h-4 w-4" />
                                Edit profile
                            </a>
                        @endcan
                    </div>

                    @if($user->bio)
                        <p class="mt-5 text-sm leading-relaxed text-gray-700">{{ $user->bio }}</p>
                    @endif
                </div>
            </div>

            {{-- Details card --}}
            <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow">
                <div class="divide-y divide-gray-100">
                    <dl class="grid grid-cols-1 gap-px sm:grid-cols-2">
                        <div class="px-6 py-5">
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Looking for</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->looking_for?->label() ?? '---' }}</dd>
                            @if($user->looking_for?->description())
                                <dd class="mt-0.5 text-xs text-gray-500">{{ $user->looking_for->description() }}</dd>
                            @endif
                        </div>

                        <div class="px-6 py-5">
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Interested in</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->interested_in?->label() ?? '---' }}</dd>
                        </div>

                        <div class="px-6 py-5">
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Age preference</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">
                                {{ $user->min_age_preference }}&ndash;{{ $user->max_age_preference }}
                                @if($user->flexible_on_age)
                                    <span class="ml-1.5 inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">Flexible</span>
                                @endif
                            </dd>
                        </div>

                        <div class="px-6 py-5">
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Member since</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->created_at->format('F j, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Interests card --}}
            @if($user->interests->isNotEmpty())
                <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow">
                    <div class="px-6 py-5">
                        <h2 class="text-xs font-medium uppercase tracking-wider text-gray-400">Interests</h2>

                        @foreach($user->interests->groupBy('category.label') as $categoryLabel => $interests)
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-700">{{ $categoryLabel }}</h3>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($interests as $interest)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-700">
                                            @if($interest->icon)
                                                <span>{{ $interest->icon }}</span>
                                            @endif
                                            {{ $interest->label }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
