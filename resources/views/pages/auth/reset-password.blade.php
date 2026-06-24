@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-label for="email">Email</x-label>
            <x-input type="email" name="email" :value="old('email', $request->email)" required autofocus />
            <x-form-error field="email" />
        </div>

        <div class="mt-4">
            <x-label for="password">New Password</x-label>
            <x-input type="password" name="password" required />
            <x-form-error field="password" />
        </div>

        <div class="mt-4">
            <x-label for="password_confirmation">Confirm Password</x-label>
            <x-input type="password" name="password_confirmation" required />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <x-button type="submit">Reset Password</x-button>
        </div>
    </form>
@endsection
