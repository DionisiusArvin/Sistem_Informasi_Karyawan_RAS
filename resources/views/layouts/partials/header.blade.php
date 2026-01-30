<header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between px-6 py-4">

        {{-- KIRI: TOGGLE + BREADCRUMB --}}
        <div class="flex items-center gap-4">

            {{-- Toggle sidebar (mobile) --}}
            <button @click.stop="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                    <path d="M4 6H20M4 12H20M4 18H11"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            {{-- Toggle collapse sidebar (desktop) --}}
            <button
                @click="sidebarCollapsed = !sidebarCollapsed; document.cookie = `sidebar_collapsed=${sidebarCollapsed};path=/;max-age=31536000`"
                class="hidden md:block text-gray-500 dark:text-gray-400 hover:text-gray-600 focus:outline-none"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
            </button>

            {{-- Breadcrumb sejajar konten --}}
            <div class="ml-4 text-gray-800 dark:text-gray-200">
                {{ Breadcrumbs::render() }}
            </div>

        </div>

        {{-- KANAN --}}
        <div class="flex items-center space-x-4">

            {{-- NOTIFIKASI (pakai style lama yang bagus) --}}
            <div class="relative">
                <button class="flex text-gray-500 dark:text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                                 a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                                 C7.67 6.165 6 8.388 6 11v3.159
                                 c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <span class="absolute top-0 right-0 inline-block w-2 h-2 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"></span>
            </div>

            {{-- DARK MODE (icon lengkap) --}}
            <button @click="darkMode = !darkMode" class="flex text-gray-500 dark:text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg x-show="!darkMode" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="darkMode" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            {{-- USER DROPDOWN --}}
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-500 text-white font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2 border-b">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">
                            {{ Auth::user()->email }}
                        </div>
                    </div>

                    <x-dropdown-link :href="route('profile.edit')">
                        Profil Saya
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            Log Out
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>

        </div>
    </div>
</header>
