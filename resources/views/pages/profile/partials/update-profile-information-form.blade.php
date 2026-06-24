@if (session('status') === 'profile-updated')
    <div class="mt-4 text-sm font-medium text-green-600">Profile updated.</div>
@endif

<form method="POST" action="{{ route('profile.update', ['user' => $user]) }}" class="mt-6 space-y-6">
    @csrf
    @method('PATCH')

    {{-- Avatar preview --}}
    <div class="flex items-center gap-4">
        <x-avatar :src="$user->avatar" :alt="$user->name" size="lg" class="border border-gray-200 bg-gray-100" />
    </div>

    {{-- Name --}}
    <div>
        <x-label for="name">Name</x-label>
        <x-input name="name" :value="old('name', $user->name)" required autofocus />
        <x-form-error field="name" bag="updateProfileInformation" />
    </div>

    {{-- Email --}}
    <div>
        <x-label for="email">Email</x-label>
        <x-input type="email" name="email" :value="old('email', $user->email)" required />
        <x-form-error field="email" bag="updateProfileInformation" />
    </div>

    {{-- Bio --}}
    <div>
        <x-label for="bio">Bio</x-label>
        <x-textarea name="bio" rows="4" maxlength="1000" placeholder="Tell people a little about yourself">{{ old('bio', $user->bio) }}</x-textarea>
        <x-form-error field="bio" bag="updateProfileInformation" />
    </div>

    {{-- Birthdate --}}
    <div>
        <x-label for="birthdate">Date of Birth</x-label>
        <x-input type="date" name="birthdate"
               :value="old('birthdate', $user->birthdate?->toDateString())" required
               :max="now()->subYears(18)->toDateString()"
               :min="now()->subYears(100)->toDateString()" />
        <x-form-error field="birthdate" bag="updateProfileInformation" />
    </div>

    {{-- Gender --}}
    <div>
        <x-label for="gender">Gender</x-label>
        <x-select-input name="gender" required>
            @foreach(\App\Enums\Gender::toSelectOptions() as $option)
                <option value="{{ $option['value'] }}" @selected(old('gender', $user->gender?->value) === $option['value'])>
                    {{ $option['label'] }}
                </option>
            @endforeach
        </x-select-input>
        <x-form-error field="gender" bag="updateProfileInformation" />
    </div>

    {{-- Looking for --}}
    <div>
        <x-label for="looking_for">Looking for</x-label>
        <x-select-input name="looking_for" required>
            @foreach(\App\Enums\LookingFor::toSelectOptions() as $option)
                <option value="{{ $option['value'] }}" @selected(old('looking_for', $user->looking_for?->value) === $option['value'])>
                    {{ $option['label'] }}
                </option>
            @endforeach
        </x-select-input>
        <x-form-error field="looking_for" bag="updateProfileInformation" />
    </div>

    {{-- Interested in --}}
    <div>
        <x-label for="interested_in">Interested in</x-label>
        <x-select-input name="interested_in" required>
            @foreach(\App\Enums\InterestedIn::toSelectOptions() as $option)
                <option value="{{ $option['value'] }}" @selected(old('interested_in', $user->interested_in?->value) === $option['value'])>
                    {{ $option['label'] }}
                </option>
            @endforeach
        </x-select-input>
        <x-form-error field="interested_in" bag="updateProfileInformation" />
    </div>

    {{-- Age preference --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-label for="min_age_preference">Minimum age</x-label>
            <x-input type="number" name="min_age_preference"
                   :value="old('min_age_preference', $user->min_age_preference)" required
                   min="18" max="99" />
            <x-form-error field="min_age_preference" bag="updateProfileInformation" />
        </div>

        <div>
            <x-label for="max_age_preference">Maximum age</x-label>
            <x-input type="number" name="max_age_preference"
                   :value="old('max_age_preference', $user->max_age_preference)" required
                   min="18" max="99" />
            <x-form-error field="max_age_preference" bag="updateProfileInformation" />
        </div>
    </div>

    {{-- Flexible on age --}}
    <div class="flex items-center gap-2">
        <input type="hidden" name="flexible_on_age" value="0">
        <input id="flexible_on_age" type="checkbox" name="flexible_on_age" value="1"
               @checked(old('flexible_on_age', $user->flexible_on_age))
               class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
        <label for="flexible_on_age" class="text-sm text-gray-700">Show me people slightly outside my age range</label>
        <x-form-error field="flexible_on_age" bag="updateProfileInformation" />
    </div>

    {{-- Interests --}}
    @if(isset($interestCategories) && $interestCategories->isNotEmpty())
        @php $selectedInterests = old('interests', $user->interestIds()); @endphp
        <div>
            <x-label>Interests</x-label>
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
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-300 px-3 py-1 text-sm text-gray-700 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500">
                                        @if($interest->icon)<span>{{ $interest->icon }}</span>@endif
                                        {{ $interest->label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <x-form-error field="interests" bag="updateProfileInformation" />
            <x-form-error field="interests.*" bag="updateProfileInformation" />
        </div>
    @endif

    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
        <x-button type="submit">Save</x-button>
    </div>
</form>
