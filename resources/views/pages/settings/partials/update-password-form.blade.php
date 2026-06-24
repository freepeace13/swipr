@if (session('status') === 'password-updated')
    <div class="mt-4 text-sm font-medium text-green-600">Password updated.</div>
@endif

<form method="POST" action="{{ route('user-password.update') }}" class="mt-6 max-w-xl space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
        <input id="current_password" type="password" name="current_password" required
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('current_password', 'updatePassword')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
        <input id="password" type="password" name="password" required
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('password', 'updatePassword')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    </div>

    <div class="flex items-center justify-end border-t border-gray-100 pt-6">
        <button type="submit"
                class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Save
        </button>
    </div>
</form>
