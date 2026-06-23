<nav class="bg-white border-b border-gray-100" x-data="{ open: false, userMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('feeds') }}" class="text-xl font-semibold text-gray-800">
                    {{ config('app.name', 'Swipr') }}
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-4">
                @auth
                    <div class="relative" @click.away="userMenu = false">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-1 text-sm text-gray-700 hover:text-gray-900">
                            {{ $auth->name }}
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>



                        <div x-show="userMenu" x-transition class="absolute right-0 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5" style="display: none;">
                            <a href="{{ route('profile', ['user' => $auth]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Info</a>
                            <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
                            <a href="{{ route('chat.inbox') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inbox</a>
                            <hr class="my-1 border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-gray-900">Register</a>
                    @endif
                @endauth
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = !open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                <p class="text-sm font-medium text-gray-700">{{ $auth->name }}</p>
                <a href="{{ route('profile', ['user' => $auth]) }}" class="block py-1 text-sm text-gray-600 hover:text-gray-900">Profile Info</a>
                <a href="{{ route('settings') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-900">Account Settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block py-1 text-sm text-gray-600 hover:text-gray-900 underline">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block text-sm text-gray-700">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block text-sm text-gray-700">Register</a>
                @endif
            @endauth
        </div>
    </div>
</nav>
