<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Swipr') }} — Find Your Person</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 antialiased" x-data="{ mobileNav: false }">

    {{-- Navigation --}}
    <nav class="fixed inset-x-0 top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="/" class="bg-gradient-to-r from-brand-500 to-accent-600 bg-clip-text text-xl font-bold text-transparent">
                {{ config('app.name', 'Swipr') }}
            </a>

            <div class="hidden items-center gap-3 sm:flex">
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-button bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-button transition hover:bg-brand-700">
                        Get started
                    </a>
                @endif
            </div>

            <button @click="mobileNav = !mobileNav" class="rounded-button p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 sm:hidden">
                <x-heroicon-o-bars-3 x-show="!mobileNav" class="h-6 w-6" />
                <x-heroicon-o-x-mark x-show="mobileNav" class="h-6 w-6" x-cloak />
            </button>
        </div>

        {{-- Mobile nav --}}
        <div x-show="mobileNav" x-transition x-cloak class="border-t border-gray-100 bg-white px-4 py-3 sm:hidden">
            <a href="{{ route('login') }}" class="block py-2 text-sm text-gray-700">Log in</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="block py-2 text-sm font-medium text-brand-600">Get started</a>
            @endif
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative overflow-hidden pt-16">
        <div class="brand-gradient-diagonal absolute inset-0"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,0.15),transparent_60%)]"></div>

        <div class="relative mx-auto flex min-h-[85vh] max-w-7xl flex-col items-center justify-center px-4 py-24 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Find your person.
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-white/80 sm:text-xl">
                Swipr connects you with people who share your interests and values. Real conversations, real connections.
            </p>

            <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-button bg-white px-8 py-3 text-sm font-semibold text-brand-600 shadow-lg transition hover:bg-gray-50">
                        Create your profile
                    </a>
                @endif
                <a href="{{ route('login') }}" class="inline-flex items-center rounded-button border-2 border-white/30 px-8 py-3 text-sm font-semibold text-white transition hover:border-white/50 hover:bg-white/10">
                    Log in
                </a>
            </div>
        </div>

        {{-- Wave divider --}}
        <div class="absolute inset-x-0 bottom-0">
            <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="h-12 w-full sm:h-16 lg:h-20">
                <path d="M0 80V20C240 60 480 0 720 20C960 40 1200 60 1440 20V80H0Z" fill="white"/>
            </svg>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Designed for real connections</h2>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600">Everything you need to find meaningful matches and start conversations that matter.</p>
            </div>

            <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Feature 1 --}}
                <div class="rounded-card bg-gray-50 p-8 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-100">
                        <x-heroicon-o-sparkles class="h-7 w-7 text-brand-600" />
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-gray-900">Smart Matching</h3>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">Our algorithm learns your preferences to show you the most compatible profiles first.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="rounded-card bg-gray-50 p-8 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-100">
                        <x-heroicon-o-chat-bubble-oval-left-ellipsis class="h-7 w-7 text-brand-600" />
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-gray-900">Real-Time Chat</h3>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">Message your matches instantly with read receipts and typing indicators built in.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="rounded-card bg-gray-50 p-8 text-center sm:col-span-2 lg:col-span-1">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-100">
                        <x-heroicon-o-heart class="h-7 w-7 text-brand-600" />
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-gray-900">Interest-Based Discovery</h3>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">Tag your interests and find people who love the same things you do.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative overflow-hidden py-20 sm:py-28">
        <div class="brand-gradient absolute inset-0"></div>
        <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Ready to find your match?</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-white/80">Join Swipr today and start meeting people who get you.</p>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="mt-8 inline-flex items-center rounded-button bg-white px-8 py-3 text-sm font-semibold text-brand-600 shadow-lg transition hover:bg-gray-50">
                    Create your profile
                </a>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-100 bg-gray-50 py-8">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <p class="bg-gradient-to-r from-brand-500 to-accent-600 bg-clip-text text-sm font-semibold text-transparent">{{ config('app.name', 'Swipr') }}</p>
            <p class="mt-2 text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Swipr') }}. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
