@if (session('status') === 'profile-updated')
    <div class="mt-4 text-sm font-medium text-green-600">Profile updated.</div>
@endif

<form method="POST" action="{{ route('profile.update', ['user' => $user]) }}" class="mt-6 space-y-6">
    @csrf
    @method('PATCH')

    {{-- Avatar preview --}}
    <div class="flex items-center gap-4">
        <img
            src="{{ $user->avatar }}"
            alt="{{ $user->name }}"
            class="h-16 w-16 rounded-full border border-gray-200 bg-gray-100 object-cover"
        >
    </div>

    {{-- Name --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('name', 'updateProfileInformation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('email', 'updateProfileInformation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Bio --}}
    <div>
        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
        <textarea id="bio" name="bio" rows="4" maxlength="1000"
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="Tell people a little about yourself">{{ old('bio', $user->bio) }}</textarea>
        @error('bio', 'updateProfileInformation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Birthdate --}}
    <div>
        <label for="birthdate" class="block text-sm font-medium text-gray-700">Date of Birth</label>
        <input id="birthdate" type="date" name="birthdate"
               value="{{ old('birthdate', $user->birthdate?->toDateString()) }}" required
               max="{{ now()->subYears(18)->toDateString() }}"
               min="{{ now()->subYears(100)->toDateString() }}"
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('birthdate', 'updateProfileInformation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Gender --}}
    <div>
        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
        <select id="gender" name="gender" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @foreach(\App\Enums\Gender::toSelectOptions() as $option)
                <option value="{{ $option['value'] }}" @selected(old('gender', $user->gender?->value) === $option['value'])>
                    {{ $option['label'] }}
                </option>
            @endforeach
        </select>
        @error('gender', 'updateProfileInformation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Looking for --}}
    <div>
        <label for="looking_for" class="block text-sm font-medium text-gray-700">Looking for</label>
        <select id="looking_for" name="looking_for" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @foreach(\App\Enums\LookingFor::toSelectOptions() as $option)
                <option value="{{ $option['value'] }}" @selected(old('looking_for', $user->looking_for?->value) === $option['value'])>
                    {{ $option['label'] }}
                </option>
            @endforeach
        </select>
        @error('looking_for', 'updateProfileInformation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Interested in --}}
    <div>
        <label for="interested_in" class="block text-sm font-medium text-gray-700">Interested in</label>
        <select id="interested_in" name="interested_in" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @foreach(\App\Enums\InterestedIn::toSelectOptions() as $option)
                <option value="{{ $option['value'] }}" @selected(old('interested_in', $user->interested_in?->value) === $option['value'])>
                    {{ $option['label'] }}
                </option>
            @endforeach
        </select>
        @error('interested_in', 'updateProfileInformation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Age preference --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="min_age_preference" class="block text-sm font-medium text-gray-700">Minimum age</label>
            <input id="min_age_preference" type="number" name="min_age_preference"
                   value="{{ old('min_age_preference', $user->min_age_preference) }}" required
                   min="18" max="99"
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('min_age_preference', 'updateProfileInformation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="max_age_preference" class="block text-sm font-medium text-gray-700">Maximum age</label>
            <input id="max_age_preference" type="number" name="max_age_preference"
                   value="{{ old('max_age_preference', $user->max_age_preference) }}" required
                   min="18" max="99"
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('max_age_preference', 'updateProfileInformation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Flexible on age --}}
    <div class="flex items-center gap-2">
        <input type="hidden" name="flexible_on_age" value="0">
        <input id="flexible_on_age" type="checkbox" name="flexible_on_age" value="1"
               @checked(old('flexible_on_age', $user->flexible_on_age))
               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <label for="flexible_on_age" class="text-sm text-gray-700">Show me people slightly outside my age range</label>
        @error('flexible_on_age', 'updateProfileInformation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Interests --}}
    @if(isset($interestCategories) && $interestCategories->isNotEmpty())
        @php $selectedInterests = old('interests', $user->interestIds()); @endphp
        <div>
            <label class="block text-sm font-medium text-gray-700">Interests</label>
            <p class="mt-1 text-xs text-gray-500">Pick the things you're into &mdash; they help us find better matches.</p>

            <div class="mt-3 space-y-5">
                @foreach($interestCategories as $category)
                    <div>
                        <h4 class="flex items-center gap-1.5 text-sm font-medium text-gray-600">
                            @if($category->icon)<span>{{ $category->icon }}</span>@endif
                            {{ $category->label }}
                        </h4>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($category->interests as $interest)
                                <label class="relative inline-flex cursor-pointer">
                                    <input type="checkbox" name="interests[]" value="{{ $interest->id }}"
                                           @checked(in_array($interest->id, $selectedInterests))
                                           class="peer sr-only">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-300 px-3 py-1 text-sm text-gray-700 transition peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">
                                        @if($interest->icon)<span>{{ $interest->icon }}</span>@endif
                                        {{ $interest->label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @error('interests', 'updateProfileInformation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('interests.*', 'updateProfileInformation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
        <button type="submit"
                class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Save
        </button>
    </div>
</form>
