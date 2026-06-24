@extends('layouts.guest')

@section('title', 'Log In')

@section('content')
    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-label for="email">Email</x-label>
            <x-input type="email" name="email" :value="old('email')" required autofocus />
            <x-form-error field="email" />
        </div>

        <div class="mt-4">
            <x-label for="password">Password</x-label>
            <x-input type="password" name="password" required />
            <x-form-error field="password" />
        </div>

        <div class="mt-4 flex items-center">
            <input id="remember_me" type="checkbox" name="remember"
                   class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
            <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
        </div>

        <div class="mt-4 flex items-center justify-between">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Forgot your password?
                </a>
            @endif

            <x-button type="submit">Log in</x-button>
        </div>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 underline">Register</a>
    </p>
@endsection
