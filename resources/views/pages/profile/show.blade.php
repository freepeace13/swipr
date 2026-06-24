@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Header card: avatar + name + basics --}}
            <x-card :padding="false">
                <div class="h-32 brand-gradient"></div>
                <div class="relative px-6 pb-6">
                    <div class="-mt-16 flex items-end gap-5">
                        <x-avatar :src="$user->avatar" :alt="$user->name" size="xl" class="border-4 border-white bg-gray-200 shadow-md" />
                        <div class="pb-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}<span class="ml-1 text-lg font-normal text-gray-500">, {{ $user->age }}</span></h1>
                            @if($user->gender)
                                <p class="mt-0.5 text-sm text-gray-500">{{ $user->gender->label() }}</p>
                            @endif
                        </div>

                        @can('edit', $user)
                            <x-button as="a" :href="route('settings', ['tab' => 'profile'])" size="sm" class="ml-auto mb-1">
                                <x-heroicon-m-pencil-square class="h-4 w-4" />
                                Edit profile
                            </x-button>
                        @endcan
                    </div>

                    @if($user->bio)
                        <p class="mt-5 text-sm leading-relaxed text-gray-700">{{ $user->bio }}</p>
                    @endif
                </div>
            </x-card>

            {{-- Details card --}}
            <x-card :padding="false" class="mt-6">
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
                                    <x-badge variant="brand" class="ml-1.5">Flexible</x-badge>
                                @endif
                            </dd>
                        </div>

                        <div class="px-6 py-5">
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Member since</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->created_at->format('F j, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </x-card>

            {{-- Interests card --}}
            @if($user->interests->isNotEmpty())
                <x-card class="mt-6">
                    <h2 class="text-xs font-medium uppercase tracking-wider text-gray-400">Interests</h2>

                    @foreach($user->interests->groupBy('category.label') as $categoryLabel => $interests)
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-700">{{ $categoryLabel }}</h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($interests as $interest)
                                    <x-badge>
                                        @if($interest->icon)
                                            <span>{{ $interest->icon }}</span>
                                        @endif
                                        {{ $interest->label }}
                                    </x-badge>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </x-card>
            @endif

        </div>
    </div>
@endsection
