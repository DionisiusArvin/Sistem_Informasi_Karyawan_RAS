<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tugas Mendadak
            </h2>
            @can('manage-ad-hoc-tasks')
                <a href="{{ route('ad-hoc-tasks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                    + Tambah Tugas
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tugas</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Ditugaskan Kepada</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Diberikan Oleh</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Batas Waktu</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($tasks as $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-4">{{ $task->name }}</td>
                                    <td class="py-4 px-4">{{ $task->assignedTo->name ?? 'N/A' }}</td>
                                    <td class="py-4 px-4">{{ $task->assignedBy->name ?? 'N/A' }}</td>
                                    <td class="py-4 px-4">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}</td>
                                    <td class="py-4 px-4">{{ $task->status }}</td>
                                    <td class="py-4 px-4">
                                        @if($task->assigned_to_id === auth()->id() && $task->status !== 'Selesai')
                                            <a href="{{ route('ad-hoc-tasks.upload.form', $task->id) }}" class="text-blue-600 hover:underline">Upload</a>
                                        @elseif($task->file_path)
                                            <a href="{{ asset('storage/' . $task->file_path) }}" target="_blank" class="text-green-600 hover:underline">Lihat File</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-500">Belum ada tugas mendadak.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>