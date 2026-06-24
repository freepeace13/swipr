@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-label for="name">Name</x-label>
            <x-input name="name" :value="old('name')" required autofocus />
            <x-form-error field="name" />
        </div>

        <div class="mt-4">
            <x-label for="email">Email</x-label>
            <x-input type="email" name="email" :value="old('email')" required />
            <x-form-error field="email" />
        </div>

        <div class="mt-4">
            <x-label for="birthdate">Date of Birth</x-label>
            <x-input type="date" name="birthdate" :value="old('birthdate')" required :max="now()->subYears(18)->toDateString()" />
            <x-form-error field="birthdate" />
        </div>

        <div class="mt-4">
            <x-label for="gender">Gender</x-label>
            <x-select-input name="gender" required>
                <option value="">Select gender</option>
                @foreach(\App\Enums\Gender::toSelectOptions() as $option)
                    <option value="{{ $option['value'] }}" @selected(old('gender') === $option['value'])>
                        {{ $option['label'] }}
                    </option>
                @endforeach
            </x-select-input>
            <x-form-error field="gender" />
        </div>

        <div class="mt-4">
            <x-label for="password">Password</x-label>
            <x-input type="password" name="password" required />
            <x-form-error field="password" />
        </div>

        <div class="mt-4">
            <x-label for="password_confirmation">Confirm Password</x-label>
            <x-input type="password" name="password_confirmation" required />
        </div>

        <div class="mt-4 flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                Already registered?
            </a>

            <x-button type="submit">Register</x-button>
        </div>
    </form>
@endsection
