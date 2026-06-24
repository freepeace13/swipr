<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Swipr') }} - @yield('title', 'Welcome')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <a href="/">
                <h1 class="bg-gradient-to-r from-brand-500 to-accent-600 bg-clip-text text-3xl font-bold text-transparent">{{ config('app.name', 'Swipr') }}</h1>
            </a>
        </div>

        <x-card class="w-full sm:max-w-md mt-6">
            @yield('content')
        </x-card>
    </div>
</body>
</html>
