<aside 
    x-cloak
    class="fixed inset-y-0 left-0 z-30 bg-gray-900 text-gray-400
           transform transition-all duration-300 ease-in-out
           md:relative md:translate-x-0
           {{ $sidebarCollapsed ? 'w-20' : 'w-64' }}
           flex flex-col overflow-y-auto no-scrollbar"
    :class="{ 'w-64': !sidebarCollapsed, 'w-20': sidebarCollapsed,
              'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
>
    <div class="flex items-center justify-center h-20 border-b border-gray-700/50 px-4 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center overflow-hidden">
            <img src="{{ asset('image/hh.png') }}" alt="Logo Perusahaan" class="h-10 w-auto">
            <h1 class="text-xl font-bold ml-2 text-white transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 scale-0 w-0' : 'opacity-100 scale-100 w-auto'">
                Dashboard<span class="text-blue-400"> Tugas</span>
            </h1>
        </a>
    </div>

    <nav class="flex-1 flex flex-col px-4 py-4">
        <div class="flex-1 overflow-y-auto no-scrollbar pr-2">
            <div class="mb-4">
            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider transition-all duration-200" :class="sidebarCollapsed ? 'opacity-0' : ''">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="relative flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">
                @if(request()->routeIs('dashboard'))<span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>@endif
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">Dashboard</span>
            </a>
        </div>

        {{-- MANAJEMEN --}}
        <div>
            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider transition-all duration-200" :class="sidebarCollapsed ? 'opacity-0' : ''">Manajemen</div>
            @can('view-project')
                <a href="{{ route('projects.index') }}" class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('projects.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">
                     @if(request()->routeIs('projects.*'))<span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>@endif
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">Proyek</span>
                </a>
            @endcan
            @if(auth()->user()->role === 'staff')
                <a href="{{ route('division-tasks.index') }}" class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('division-tasks.index') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">
                    @if(request()->routeIs('division-tasks.index'))<span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>@endif
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">Tugas Divisi</span>
                </a>
            @endif
            @if(auth()->user()->role === 'staff')
                <a href="{{ route('ad-hoc-tasks.index') }}" class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('ad-hoc-tasks.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">
                    @if(request()->routeIs('ad-hoc-tasks.*'))<span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>@endif
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">Tugas Mendadak</span>
                </a>
            @endif
            {{-- MENU BARU: VALIDASI (hanya untuk kepala divisi) --}}
            @if(auth()->user()->role === 'kepala_divisi')
                <a 
                    href="{{ route('validation.index') }}" 
                    class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 
                        {{ request()->routeIs('validation.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">
                    @if(request()->routeIs('validation.*'))
                        <span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>
                    @endif
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" 
                        :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">
                        Validasi
                    </span>
                </a>
            @endif
            @if(auth()->user()->role === 'kepala_divisi')
                <a 
                    href="{{ route('schedules.index') }}" 
                    class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 
                        {{ request()->routeIs('schedules.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">
                    
                    @if(request()->routeIs('schedules.*'))
                        <span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>
                    @endif

                    <!-- Icon Jadwal Baru -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>

                    <span class="ml-4 font-medium"
                        :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">
                        Jadwal
                    </span>
                </a>
            @endif
            {{-- MENU BARU: CUTI --}}
            @if(in_array(auth()->user()->role, ['staff','admin','kepala_divisi','manager']))
                <a 
                    href="{{ route('leaves.index') }}" 
                    class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200
                        {{ request()->routeIs('leaves.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">

                    @if(request()->routeIs('leaves.*'))
                        <span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>
                    @endif

                    <!-- Icon Cuti Baru -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A7 7 0 1118 10m0 0v4m0-4h-4" />
                    </svg>

                    <span class="ml-4 font-medium"
                        :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">
                        Cuti
                    </span>
                </a>
            @endif
            {{-- MENU BARU: LAPORAN --}}
            @if(auth()->user()->role === 'manager' || auth()->user()->role === 'kepala_divisi')
                <a 
                    href="{{ route('reports.index') }}" 
                    class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('reports.index') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}"
                >
                    @if(request()->routeIs('reports.index'))
                        <span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>
                    @endif
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">Laporan</span>
                </a>
            @endif

            {{-- MENU BARU: LAPORAN ADMIN --}}
            @if(auth()->user()->role === 'manager' || auth()->user()->role === 'admin')
                <a 
                    href="{{ route('reports.admin-tasks') }}" 
                    class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('reports.admin-tasks') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}"
                >
                    @if(request()->routeIs('reports.admin-tasks'))
                        <span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>
                    @endif
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">Laporan Admin</span>
                </a>
            @endif
            
        {{-- MENU BARU: PERFORMA KARYAWAN (Manager) --}}
        @if(auth()->user()->role === 'manager')
        <a 
            href="{{ route('performance.index') }}" 
            class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 
            {{ request()->routeIs('performance.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">

            @if(request()->routeIs('performance.*'))
                <span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>
            @endif

            <!-- ICON KPI / CHART -->
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3v18h18M7 13l3-3 4 4 5-5" />
            </svg>

            <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap"
                :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">
                Performa Karyawan
            </span>
        </a>
        @endif

             {{-- MENU BARU: TUGAS ADMIN --}}
            @if(auth()->user()->role === 'manager' || auth()->user()->role === 'admin')
                <a href="{{ route('admin-tasks.index') }}" class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 hover:bg-gray-700/50">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A6.997 6.997 0 0112 4.354a6.997 6.997 0 014.5 9.45M12 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">Tugas Admin</span>
                </a>
            @endif
            {{-- MENU BARU: TUGAS MENDADAK --}}
            @can('manage-ad-hoc-tasks')
                <a href="{{ route('ad-hoc-tasks.index') }}" class="relative flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('ad-hoc-tasks.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">
                    @if(request()->routeIs('ad-hoc-tasks.*'))<span class="absolute left-0 top-0 h-full w-1 bg-blue-400 rounded-r-full"></span>@endif
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span class="ml-4 font-medium transition-all duration-200 whitespace-nowrap" :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">Tugas Mendadak</span>
                </a>
            @endcan
        </div>
    </div>
    {{-- PENGATURAN --}}
    <div class="space-y-2 pt-4">

        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider"
            :class="sidebarCollapsed ? 'opacity-0' : ''">
            Pengaturan
        </div>

    {{-- PROFIL --}}
    <a href="{{ route('profile.edit') }}"
       class="relative flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200
              {{ request()->routeIs('profile.edit') ? 'bg-blue-600 text-white' : 'hover:bg-gray-700/50' }}">

        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066
                     c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572
                     c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573
                     c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065
                     c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066
                     c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572
                     c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573
                     c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>

        <span class="ml-4 font-medium whitespace-nowrap"
              :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">
            Profil
        </span>
    </a>

    {{-- LOGOUT --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); this.closest('form').submit();"
           class="relative flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 hover:bg-gray-700/50">

            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>

            <span class="ml-4 font-medium whitespace-nowrap"
                  :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">
                Logout
            </span>
        </a>
    </form>

    {{-- USER PROFILE (INI YANG TADI NYASAR) --}}
    <div class="pt-4 mt-4 border-t border-gray-700/50 px-0">
        <a href="{{ route('profile.edit') }}"
           class="flex items-center w-full p-2 rounded-lg hover:bg-gray-700/50">

            <div class="flex-shrink-0 flex items-center justify-center h-8 w-8
                        rounded-full bg-blue-500 text-white font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>

            <div class="ml-3 whitespace-nowrap"
                 :class="sidebarCollapsed ? 'opacity-0 hidden' : ''">
                <p class="text-sm font-semibold text-white">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-gray-400 capitalize">
                    {{ str_replace('_', ' ', Auth::user()->role) }}
                </p>
            </div>
        </a>
    </div>
</nav>
</aside>
