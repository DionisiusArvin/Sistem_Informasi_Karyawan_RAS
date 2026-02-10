<x-app-layout>
    <x-slot name="header">
        <span class="dark:text-gray-200">
            Tugas Divisi
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Bagian: Tugas Tersedia --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg border dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tugas Tersedia</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-gray-800">
                        <thead class="bg-gray-200 dark:bg-gray-700">
                            <tr>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Nama Tugas</th>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Proyek</th>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Batas Waktu</th>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-300">
                            @forelse ($tasks->where('assigned_to_staff_id', null) as $task)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="py-3 px-4">
                                        <div class="text-normal mt-1 font-medium dark:text-gray-100">{{ $task->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $task->description }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center">{{ $task->task->project->name ?? '-' }}</td>
                                    <td class="text-center py-3 px-4">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                    <td class="text-center py-3 px-4">
                                        <form action="{{ route('dailytasks.take', $task->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm text-green-600 dark:text-green-400 hover:underline font-semibold">Ambil Tugas</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 dark:text-gray-400">Tidak ada tugas yang tersedia saat ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bagian: Tugas Saya --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg border dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tugas Saya</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-gray-800">
                        <thead class="bg-gray-200 dark:bg-gray-700">
                            <tr>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Nama Tugas</th>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Proyek</th>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Batas Waktu</th>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Progress</th>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Status</th>
                                <th class="text-center py-3 px-4 uppercase font-semibold text-sm dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-300">
                            @forelse ($tasks->where('assigned_to_staff_id', Auth::id()) as $task)
                                <tr x-data="{ showModal: false }" class="border-b dark:border-gray-700">
                                    <td class="py-3 px-4">
                                        <div class="text-normal mt-1 font-medium dark:text-gray-100">{{ $task->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $task->description }}</div>
                                    </td>
                                    <td class="text-center py-3 px-4">{{ $task->task->project->name ?? '-' }}</td>
                                    <td class="text-center py-3 px-4">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center">
                                            <span class="mr-2 text-sm">{{ $task->progress }}%</span>
                                            <div class="w-24 bg-gray-200 dark:bg-gray-600 h-2.5 rounded-full overflow-hidden">
                                                <div class="bg-blue-600 h-full" style="width: {{ $task->progress }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3 px-4 font-semibold">
                                        @if ($task->status === 'Selesai')
                                            <span class="text-green-600 dark:text-green-400">
                                                Selesai <span class="text-xs font-normal italic">({{ $task->completion_status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }})</span>
                                            </span>
                                        @elseif($task->status === 'Revisi')
                                            <button @click="showModal = 'revisi'" class="text-red-600 dark:text-red-400 hover:underline">Revisi (Catatan)</button>
                                        @elseif($task->status === 'Lanjutkan')
                                            <button @click="showModal = 'Lanjutkan'" class="text-blue-600 dark:text-blue-400 hover:underline">Lanjutkan (Catatan)</button>
                                        @else
                                            <span class="dark:text-gray-400">{{ $task->status }}</span>
                                        @endif

                                        {{-- Modal Catatan (Revisi / Lanjutkan) --}}
                                        <template x-if="showModal">
                                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
                                                <div @click.away="showModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md border dark:border-gray-700 text-left">
                                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2" x-text="showModal === 'revisi' ? 'Catatan Revisi' : 'Catatan Lanjutkan'"></h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $task->name }}</p>
                                                    <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-md border dark:border-gray-700">
                                                        <p class="text-gray-800 dark:text-gray-200">
                                                            @{{ showModal === 'revisi' ? '{{ $task->activities->where('activity_type', 'permintaan_revisi')->last()->notes ?? 'Tidak ada catatan.' }}' : '{{ $task->activities->where('activity_type', 'lanjutkan_tugas')->last()->notes ?? 'Tidak ada catatan.' }}' }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-6 flex justify-end">
                                                        <button @click="showModal = false" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="text-center py-3 px-4">
                                        <a href="{{ route('dailytasks.upload.form', $task->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Upload</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-6 dark:text-gray-400">Anda belum memiliki tugas yang sedang dikerjakan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>