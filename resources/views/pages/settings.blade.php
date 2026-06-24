@extends('layouts.app')

@section('title', 'Account Settings')

@section('bodyClass', 'overflow-hidden')

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 lg:flex-row">
                {{-- Sidebar --}}
                <aside class="lg:w-64 lg:flex-shrink-0">

                    <nav class="space-y-1">
                        @php
                            $links = [
                                'profile' => 'Profile Information',
                                'account' => 'Account',
                            ];
                        @endphp

                        @foreach ($links as $key => $label)
                            <a href="{{ route('settings', ['tab' => $key]) }}"
                               @class([
                                   'block rounded-button px-3 py-2 text-sm font-medium',
                                   'bg-brand-50 text-brand-700' => $tab === $key,
                                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => $tab !== $key,
                               ])
                               aria-current="{{ $tab === $key ? 'page' : 'false' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </nav>
                </aside>

                {{-- Content --}}
                <div class="min-w-0 flex-1">
                    <x-card>
                        @if ($tab === 'account')
                            <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                            <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>

                            @include('pages.settings.partials.update-password-form')
                        @else
                            <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
                            <p class="mt-1 text-sm text-gray-600">Update your profile information and matchmaking preferences.</p>

                            @include('pages.profile.partials.update-profile-information-form', ['user' => $auth, 'interestCategories' => $interestCategories])
                        @endif
                    </x-card>
                </div>
            </div>
        </div>
    </div>
@endsection
