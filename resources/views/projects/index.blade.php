<x-app-layout>
    <x-slot name="header">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4 md:mb-0">
            Daftar Proyek
        </h2>
        
        <div class="flex items-center space-x-2">
            <div class="flex bg-gray-200 dark:bg-gray-700 p-1 rounded-lg text-sm ">
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Layout Grid untuk Kartu Proyek --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($projects as $project)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transform hover:-translate-y-1 transition-transform duration-300">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $project->name }}</h3>
                                    @if($project->kode_proyek)
                                        <p class="text-xs font-mono text-gray-400 bg-gray-100 dark:bg-gray-700 inline-block px-2 py-0.5 rounded mt-1">{{ $project->kode_proyek }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->client_name }}</p>
                                </div>
                                @php
                                    $health = $project->getHealthStatus();
                                    $colorClass = match ($health) {
                                        'aman' => 'bg-green-100 text-green-800',
                                        'perhatian' => 'bg-yellow-100 text-yellow-800',
                                        'bahaya' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full {{ $colorClass }}">
                                    {{ ucfirst($health) }}
                                </span>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Progress</p>
                                <div class="flex items-center mt-1">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                        <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $project->getProgressPercentage() }}%"></div>
                                    </div>
                                    <span class="ml-3 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $project->getProgressPercentage() }}%</span>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Periode:</p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                                    </p>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('projects.show', $project->id) }}" class="text-gray-400 hover:text-blue-500" title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @can('manage-projects')
                                    <a href="{{ route('projects.edit', $project->id) }}" class="text-gray-400 hover:text-yellow-500" title="Edit">
                                       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus proyek ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center text-gray-500">
                        Belum ada proyek yang dibuat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>