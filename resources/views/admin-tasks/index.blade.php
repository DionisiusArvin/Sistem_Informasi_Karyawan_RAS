<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4 md:mb-0">
                Tugas Admin
            </h2>
            
            <div class="flex items-center space-x-2">
                <div class="flex bg-gray-200 dark:bg-gray-700 p-1 rounded-lg text-sm">
                    <a href="{{ route('admin-tasks.index', ['type' => 'all']) }}" class="px-3 py-1 rounded-md transition {{ $filterType == 'all' ? 'bg-white dark:bg-gray-900 text-blue-600 font-semibold shadow' : 'text-gray-500 hover:text-gray-700' }}">Semua</a>
                    <a href="{{ route('admin-tasks.index', ['type' => 'project']) }}" class="px-3 py-1 rounded-md transition {{ $filterType == 'project' ? 'bg-white dark:bg-gray-900 text-blue-600 font-semibold shadow' : 'text-gray-500 hover:text-gray-700' }}">Proyek</a>
                    <a href="{{ route('admin-tasks.index', ['type' => 'non-project']) }}" class="px-3 py-1 rounded-md transition {{ $filterType == 'non-project' ? 'bg-white dark:bg-gray-900 text-blue-600 font-semibold shadow' : 'text-gray-500 hover:text-gray-700' }}">Non-Proyek</a>
                </div>

                @can('manage-admin-tasks')
                    <a href="{{ route('admin-tasks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow-md hover:bg-blue-700">
                        + Tambah Tugas
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="space-y-4">
                        @forelse ($tasks as $task)
                            <div class="border dark:border-gray-700 rounded-lg p-4 flex flex-col md:flex-row justify-between items-start hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div class="flex-grow">
                                    <div class="flex items-center space-x-3">
                                        @if($task->project)
                                            <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-indigo-100 text-indigo-800">Proyek</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-gray-200 text-gray-800">Non-Proyek</span>
                                        @endif
                                        <p class="font-bold text-lg text-gray-900 dark:text-white">{{ $task->name }}</p>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 ml-4 pl-5 border-l-2 border-gray-200">{{ $task->description }}</p>
                                    @if($task->project)
                                        <p class="text-sm text-gray-500 mt-2 ml-4 pl-5 border-l-2 border-gray-200">Proyek Terkait: <span class="font-semibold">{{ $task->project->name }}</span></p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 mt-4 md:mt-0 md:ml-6 text-right">
                                    @php
                                        $statusColor = match ($task->status) { 'Selesai' => 'bg-green-100 text-green-800', 'Belum Dikerjakan' => 'bg-yellow-100 text-yellow-800', default => 'bg-gray-100 text-gray-800' };
                                    @endphp
                                    <span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full {{ $statusColor }}">{{ $task->status }}</span>
                                    
                                {{-- ======================= LOKASI TOMBOL AKSI ======================= --}}
                                    <div class="mt-3 flex items-center justify-end space-x-3 text-sm">
                                        @if(auth()->user()->role === 'admin' && $task->status !== 'Selesai')
                                            {{-- Admin hanya bisa upload jika belum selesai --}}
                                            <a href="{{ route('admin-tasks.upload.form', $task->id) }}" class="font-medium text-blue-600 hover:underline">
                                                Upload
                                            </a>
                                        @elseif(in_array(auth()->user()->role, ['manager', 'kepala_divisi']))
                                            <div class="flex items-center space-x-4">
                                                @if($task->file_path)
                                                    {{-- Lihat File --}}
                                                    <a href="{{ asset('storage/' . $task->file_path) }}" target="_blank" class="text-green-600 hover:underline">
                                                        Lihat File
                                                    </a>

                                                    {{-- Download File --}}
                                                    <a href="{{ route('admin-tasks.downloadFile', $task->id) }}" class="text-blue-600 hover:underline">
                                                        Download
                                                    </a>
                                                @endif

                                                @if($task->link)
                                                    <a href="{{ $task->link }}" target="_blank" class="text-blue-600 hover:underline">
                                                        Lihat Link
                                                    </a>
                                                @endif

                                                {{-- Tampilkan catatan jika ada --}}
                                                @if($task->notes)
                                                    <span class="text-gray-500 italic">Catatan: {{ $task->notes }}</span>
                                                @endif

                                                {{-- Hanya manager yg bisa edit / hapus --}}
                                                @if(auth()->user()->role === 'manager')
                                                    <a href="{{ route('admin-tasks.edit', $task->id) }}" class="text-gray-500 hover:text-blue-600">Edit</a>
                                                    <form action="{{ route('admin-tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tugas ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-gray-500 hover:text-red-600">Hapus</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-500">Belum ada tugas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>