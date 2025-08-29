<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Proyek: {{ $project->name }}
        </h2>
        {{-- Kode Proyek ditampilkan di bawah judul --}}
        @if($project->kode_proyek)
            <p class="text-sm mt-1 text-gray-600 dark:text-gray-300">
                <span class="font-semibold">Kode Proyek:</span>
                <span class="font-mono">{{ $project->kode_proyek }}</span>
            </p>
        @endif
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">   
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Progress Proyek</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->getProgressPercentage() }}%</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    @php
                        $health = $project->getHealthStatus();
                        $color = match ($health) { 'aman' => 'green', 'perhatian' => 'yellow', 'bahaya' => 'red', default => 'gray' };
                    @endphp
                    <div class="flex items-center">
                        <div class="p-3 bg-{{$color}}-100 rounded-full">
                            <svg class="w-6 h-6 text-{{$color}}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Kondisi Proyek</p>
                            <p class="text-2xl font-bold text-{{$color}}-600 capitalize">{{ $health }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-full">
                           <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Sisa Waktu</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($project->end_date)->startOfDay(), false) }} Hari</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                     <div class="flex items-center">
                        <div class="p-3 bg-pink-100 rounded-full">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Tugas Utama</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->tasks->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tugas Utama</h3>
                        @if(auth()->user()->role === 'kepala_divisi')
                            <a href="{{ route('projects.tasks.create', $project->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow-md hover:bg-blue-700">
                                + Tambah Tugas
                            </a>
                        @endif
                    </div>
                    <table class="min-w-full bg-white dark:bg-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="w-2/5 text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Tugas & Deskripsi</th>
                                <th class="w-1/5 text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Divisi</th>
                                <th class="w-1/5 text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Progress</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-400 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($project->tasks as $task)
                                <tr>
                                    <td class="py-4 px-4">
                                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $task->name }}</p>
                                        @if($task->description)
                                            <p class="text-sm text-gray-500 mt-1 italic">{{ $task->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($task->divisions as $division)
                                                <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-gray-100 text-gray-700">
                                                    {{ $division->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <span class="mr-2 text-sm">{{ $task->getProgressPercentage() }}%</span>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $task->getProgressPercentage() }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-600 hover:underline font-medium">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-500">Belum ada tugas utama.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Tugas Administrasi
            </h3>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin-tasks.create', ['project_id' => $project->id]) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">
                    + Tambah Tugas Administrasi
                </a>
            @endif
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($project->adminTasks as $adminTask)
                <div class="py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $adminTask->name }}</p>
                            <p class="text-sm text-gray-500">
                                Admin: {{ $adminTask->assignedToAdmin->name }}
                            </p>
                        </div>
                        @php
                            $statusColor = $adminTask->status === 'Selesai' 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-yellow-100 text-yellow-800';
                        @endphp
                        <span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full {{ $statusColor }}">
                            {{ $adminTask->status }}
                        </span>
                    </div>

                    {{-- Hanya manager & kepala divisi yang bisa lihat detail --}}
                    <div class="mt-3 flex items-center justify-end space-x-3 text-sm">
                        @if(auth()->user()->role === 'admin' && $adminTask->status !== 'Selesai')
                            {{-- Admin hanya bisa upload jika belum selesai --}}
                            <a href="{{ route('admin-tasks.upload.form', $adminTask->id) }}" class="font-medium text-blue-600 hover:underline">
                                Upload
                            </a>
                        @elseif(in_array(auth()->user()->role, ['manager', 'kepala_divisi']))
                            <div class="flex items-center space-x-4">
                                @if($adminTask->file_path)
                                    {{-- Lihat File --}}
                                    <a href="{{ asset('storage/' . $adminTask->file_path) }}" target="_blank" class="text-green-600 hover:underline">
                                        Lihat File
                                    </a>

                                    {{-- Download File --}}
                                    <a href="{{ route('admin-tasks.downloadFile', $adminTask->id) }}" class="text-blue-600 hover:underline">
                                        Download
                                    </a>
                                @endif

                                @if ($adminTask->link)
                                    <a href="{{ $adminTask->link }}" target="_blank" class="text-blue-600 hover:underline">
                                        Lihat Link
                                    </a>
                                @endif

                                {{-- Tampilkan catatan jika ada --}}
                                @if($adminTask->notes)
                                    <span class="text-gray-500 italic">Catatan: {{ $adminTask->notes }}</span>
                                @endif
                                
                                {{-- Hanya manager yg bisa edit / hapus --}}
                                @if(auth()->user()->role === 'manager')
                                    <a href="{{ route('admin-tasks.edit', $adminTask->id) }}" class="text-gray-500 hover:text-blue-600">Edit</a>
                                    <form action="{{ route('admin-tasks.destroy', $adminTask->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tugas ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-500 hover:text-red-600">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center py-4 text-gray-500">Belum ada tugas administrasi.</p>
            @endforelse
        </div>
    </div>
</div>

        </div>
    </div>
</x-app-layout>