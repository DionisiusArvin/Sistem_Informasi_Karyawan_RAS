<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="font-semibold text-x15 text-gray-800 dark:text-gray-200 leading-tight mb-4 md:mb-0">
                Daftar Proyek
            </h2>

            <div class="flex items-center space-x-2">
                <div class="flex bg-gray-200 dark:bg-gray-700 p-1 rounded-lg text-sm">
                    <a 
                        href="{{ route('projects.index', ['status' => 'on-progress']) }}"
                        class="px-3 py-1 rounded-md transition {{ $statusFilter == 'on-progress' ? 'bg-white dark:bg-gray-900 text-blue-600 font-semibold shadow' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        On Progress
                    </a>
                    <a 
                        href="{{ route('projects.index', ['status' => 'finished']) }}"
                        class="px-3 py-1 rounded-md transition {{ $statusFilter == 'finished' ? 'bg-white dark:bg-gray-900 text-blue-600 font-semibold shadow' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        Selesai
                    </a>
                    <a 
                        href="{{ route('projects.index', ['status' => 'all']) }}"
                        class="px-3 py-1 rounded-md transition {{ $statusFilter == 'all' ? 'bg-white dark:bg-gray-900 text-blue-600 font-semibold shadow' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        Semua
                    </a>
                </div>

                @can('manage-projects')
                    <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow-md hover:bg-blue-700">
                        + Buat Proyek
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div 
        x-data="{ 
            search: '', 
            listView: false,
            results: @js(view('projects.partials.grid', ['projects' => $projects])->render()), 
            loading: false
        }"
        class="px-1 md:px-2"
    >
        <!-- Bagian tengah: Switch dan Search sejajar di tengah -->
        <div class="flex flex-col md:flex-row gap-4 py-2">
            <!-- Search Box -->
            <input 
                type="text" 
                placeholder="Cari proyek..."
                x-ref="searchBox"
                @input.debounce.500ms="
                    search = $refs.searchBox.value;
                    loading = true;
                    let baseUrl = '{{ route('projects.index') }}?status={{ $statusFilter }}';
                    let url = search.trim() === '' 
                        ? baseUrl 
                        : baseUrl + '&search=' + encodeURIComponent(search);

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                        .then(res => res.text())
                        .then(html => { 
                            results = html; 
                            loading = false;
                        })
                        .catch(() => loading = false);
                "
                class="px-3 py-2 w-64 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none text-sm"
            />
            <!-- Switch Grid/List -->
            <label class="flex items-center cursor-pointer">
                <span class="text-md text-gray-800 dark:text-gray-200 mr-2">Grid</span>
                <input type="checkbox" x-model="listView" class="hidden" />
                <div class="relative">
                    <div class="block bg-gray-500 dark:bg-gray-500 w-10 h-6 rounded-full"></div>
                    <div
                        class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform"
                        :class="listView ? 'translate-x-4 bg-blue-500' : ''"
                    ></div>
                </div>
                <span class="text-md text-gray-800 dark:text-gray-200 ml-2">List</span>
            </label>
        </div>

        <!-- Loading Spinner -->
        <div x-show="loading" class="flex justify-center py-4">
            <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span class="ml-2 text-blue-600 text-sm">Memuat data...</span>
        </div>

        <!-- Grid/List Proyek -->
        <div>
            <template x-if="!listView">
                <div x-html="results"></div>
            </template>

            <template x-if="listView">
                @include('projects.partials.list', ['projects' => $projects])
            </template>
        </div>
</x-app-layout>
