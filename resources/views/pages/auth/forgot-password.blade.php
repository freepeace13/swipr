@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
    <div class="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Just enter your email address and we will email you a password reset link.
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-label for="email">Email</x-label>
            <x-input type="email" name="email" :value="old('email')" required autofocus />
            <x-form-error field="email" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <x-button type="submit">Email Password Reset Link</x-button>
        </div>
    </form>
@endsection
