<nav class="relative z-20 bg-white border-b border-gray-100" x-data="{ open: false, userMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('feeds') }}" class="bg-gradient-to-r from-brand-500 to-accent-600 bg-clip-text text-xl font-bold text-transparent">
                    {{ config('app.name', 'Swipr') }}
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center gap-4">
                @auth
                    <a href="{{ route('feeds') }}" class="flex items-center gap-1.5 text-sm font-medium {{ request()->routeIs('feeds') ? 'text-brand-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <x-heroicon-o-squares-2x2 class="h-5 w-5" />
                        Feeds
                    </a>
                    <a href="{{ route('chat.inbox') }}" class="flex items-center gap-1.5 text-sm font-medium {{ request()->routeIs('chat.inbox', 'chat.conversations.*') ? 'text-brand-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <x-heroicon-o-envelope class="h-5 w-5" />
                        Inbox
                    </a>

                    <div class="relative" @click.away="userMenu = false">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-1 text-sm text-gray-700 hover:text-gray-900">
                            {{ $auth->email }}
                            <x-heroicon-m-chevron-down class="h-4 w-4" />
                        </button>

                        <div x-show="userMenu" x-transition class="absolute right-0 mt-2 w-48 rounded-dropdown bg-white py-1 shadow-dropdown ring-1 ring-black/5" style="display: none;">
                            <a href="{{ route('profile.show', ['user' => $auth]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                            <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Settings</a>
                            <hr class="my-1 border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">
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

            <div class="flex items-center gap-3 sm:hidden">
                @auth
                    <a href="{{ route('feeds') }}" class="p-2 rounded-button {{ request()->routeIs('feeds') ? 'text-brand-600' : 'text-gray-400 hover:text-gray-500' }}">
                        <x-heroicon-o-squares-2x2 class="h-6 w-6" />
                    </a>
                    <a href="{{ route('chat.inbox') }}" class="p-2 rounded-button {{ request()->routeIs('chat.inbox', 'chat.conversations.*') ? 'text-brand-600' : 'text-gray-400 hover:text-gray-500' }}">
                        <x-heroicon-o-envelope class="h-6 w-6" />
                    </a>
                @endauth
                <button @click="open = !open" class="p-2 rounded-button text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <x-heroicon-o-bars-3 x-show="!open" class="h-6 w-6" />
                    <x-heroicon-o-x-mark x-show="open" class="h-6 w-6" style="display: none;" />
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                <p class="text-sm font-medium text-gray-700">{{ $auth->name }}</p>
                <a href="{{ route('profile.show', ['user' => $auth]) }}" class="block py-1 text-sm text-gray-600 hover:text-gray-900">Profile</a>
                <a href="{{ route('settings') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-900">Account</a>
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
