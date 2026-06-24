@if (session('status') === 'password-updated')
    <div class="mt-4 text-sm font-medium text-green-600">Password updated.</div>
@endif

<form method="POST" action="{{ route('user-password.update') }}" class="mt-6 max-w-xl space-y-4">
    @csrf
    @method('PUT')

    <div>
        <x-label for="current_password">Current Password</x-label>
        <x-input type="password" name="current_password" required />
        <x-form-error field="current_password" bag="updatePassword" />
    </div>

    <div>
        <x-label for="password">New Password</x-label>
        <x-input type="password" name="password" required />
        <x-form-error field="password" bag="updatePassword" />
    </div>

    <div>
        <x-label for="password_confirmation">Confirm Password</x-label>
        <x-input type="password" name="password_confirmation" required />
    </div>

    <div class="flex items-center justify-end border-t border-gray-100 pt-6">
        <x-button type="submit">Save</x-button>
    </div>
</form>
