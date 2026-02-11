<header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between px-6 py-4">

        {{-- KIRI --}}
        <div class="flex items-center gap-4">

            <button @click.stop="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">  
                    <path d="M4 6H20M4 12H20M4 18H11"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <button
                @click="sidebarCollapsed = !sidebarCollapsed; document.cookie = `sidebar_collapsed=${sidebarCollapsed};path=/;max-age=31536000`"
                class="hidden md:block text-gray-500 dark:text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
            </button>

            <div class="ml-4 text-gray-800 dark:text-gray-200">
                {{ Breadcrumbs::render() }}
            </div>
        </div>

        {{-- KANAN --}}
        <div class="flex items-center space-x-4">

            {{-- 🔔 NOTIFICATION --}}
            @php
                $notifs = \App\Models\Notification::where('user_id', auth()->id())
                            ->latest()
                            ->take(5)
                            ->get();
                $notifCount = $notifs->where('is_read', false)->count();
            @endphp

            <div x-data="{ open: false }" class="relative">

                <button @click="open = !open"
                    class="flex items-center justify-center text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-7 w-7"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                                 a6.002 6.002 0 00-4-5.659V5
                                 a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159
                                 c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1
                                 a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>

                    @if($notifCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1.5 rounded-full">
                            {{ $notifCount }}
                        </span>
                    @endif
                </button>

                {{-- DROPDOWN --}}
                <div x-show="open"
                     @click.outside="open = false"
                     x-transition
                     class="absolute right-0 mt-3 w-80 
                            bg-white dark:bg-gray-800
                            border border-gray-200 dark:border-gray-700
                            rounded-lg shadow-xl z-50">

                    {{-- HEADER NOTIF --}}
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700
                                flex justify-between items-center">

                        <span class="font-semibold text-gray-800 dark:text-white">
                            🔔 Notifikasi
                        </span>

                        <button onclick="markAllRead()"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            Baca semua
                        </button>
                    </div>

                    @forelse($notifs as $notif)
                        <div class="relative group">
                            <a href="{{ $notif->url }}"
                               onclick="markAsRead(event, {{ $notif->id }}, this.href)"
                               class="block px-4 py-3 border-b
                                      border-gray-200 dark:border-gray-700
                                      hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">

                                <div class="text-gray-800 dark:text-white font-semibold">
                                    {{ $notif->title }}
                                </div>
                                <div class="text-gray-600 dark:text-gray-300 text-xs">
                                    {{ $notif->message }}
                                </div>
                            </a>

                            <button onclick="deleteNotif(event, {{ $notif->id }})"
                                class="absolute top-2 right-2 hidden group-hover:block
                                       text-red-500 text-xs">
                                ✕
                            </button>
                        </div>
                    @empty
                        <div class="p-4 text-gray-500 dark:text-gray-400 text-center">
                            Tidak ada notifikasi
                        </div>
                    @endforelse

                    <a href="{{ route('notif.index') }}"
                       class="block text-center py-2 
                              text-blue-600 dark:text-blue-400
                              hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                        Lihat semua
                    </a>
                </div>
            </div>

            {{-- 🌙 DARK MODE --}}
            <button @click="darkMode = !darkMode"
                class="flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg x-show="!darkMode" class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="darkMode" class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            {{-- 👤 USER --}}
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center justify-center">
                        <div class="h-8 w-8 rounded-full bg-blue-500 text-white font-bold flex items-center justify-center">
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
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Log Out
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>


        </div>
    </div>

<script>
function markAsRead(e, id, url) {
    e.preventDefault();
    fetch(`/notif/read/${id}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href = url);
}

function markAllRead() {
    fetch('/notif/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload());
}

function deleteNotif(e, id) {
    e.stopPropagation();
    fetch(`/notif/delete/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload());
}
</script>

</header>
