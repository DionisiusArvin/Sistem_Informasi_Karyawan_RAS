<x-app-layout>
    <x-slot name="header">
        <span>
            Tugas Divisi
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tugas Tersedia</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Nama Tugas</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Proyek</th>
                            <th class="text-cenetr py-3 px-4 uppercase font-semibold text-sm">Batas Waktu</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($tasks->where('assigned_to_staff_id', null) as $task)
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="text-normal mt-1">{{ $task->name }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ $task->description }}</div>
                                </td>
                                <td class="py-3 px-4">{{ $task->task->project->name ?? '-' }}</td>
                                <td class="text-center py-3 px-4">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                <td class="text-center py-3 px-4">
                                    <form action="{{ route('dailytasks.take', $task->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm text-green-600 hover:underline font-semibold">Ambil Tugas</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4">Tidak ada tugas yang tersedia saat ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tugas Saya</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Nama Tugas</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Proyek</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Batas Waktu</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Progress</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Status</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($tasks->where('assigned_to_staff_id', Auth::id()) as $task)
                            <tr x-data="{ showModal: false }">
                                <td class="py-3 px-4">
                                    <div class="text-normal mt-1">{{ $task->name }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ $task->description }}</div>
                                </td>
                                <td class="text-center py-3 px-4">{{ $task->task->project->name ?? '-' }}</td>
                                <td class="text-center py-3 px-4">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                <td class="text-center py-3 px-4">
                                    <div class="flex items-center">
                                        {{-- Tambahkan span ini untuk menampilkan angka --}}
                                        <span class="mr-2 text-sm">{{ $task->status_based_progress }}%</span>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $task->status_based_progress }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center py-3 px-4 font-semibold">
                                    {{-- Status Selesai --}}
                                    @if ($task->status === 'Selesai')
                                        <span class="text-green-600">
                                            Selesai
                                            <span class="font-normal italic">
                                                ({{ $task->completion_status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }})
                                            </span>
                                        </span>

                                    {{-- Status Revisi --}}
                                    @elseif($task->status === 'Revisi')
                                        <button @click="showModal = 'revisi'" class="font-semibold text-red-600 hover:underline">
                                            Revisi (Lihat Catatan)
                                        </button>

                                    {{-- Status Lanjutkan (BARU) --}}
                                    @elseif($task->status === 'Lanjutkan')
                                        <button @click="showModal = 'Lanjutkan'" class="font-semibold text-blue-600 hover:underline">
                                            Lanjutkan (Lihat Catatan)
                                        </button>

                                    {{-- Status lain --}}
                                    @else
                                        {{ $task->status }}
                                    @endif


                                    {{-- Modal Revisi --}}
                                    <div x-show="showModal === 'revisi'" @keydown.escape.window="showModal = false"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                        style="display: none;">
                                        <div @click.away="showModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan Revisi</h3>
                                            <p class="text-sm text-gray-500 mb-4">Untuk tugas: {{ $task->name }}</p>
                                            <div class="bg-gray-100 p-4 rounded-md">
                                                <p class="text-gray-800">
                                                    {{ $task->activities->where('activity_type', 'permintaan_revisi')->last()->notes ?? 'Tidak ada catatan.' }}
                                                </p>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <button @click="showModal = false" class="px-4 py-2 bg-gray-600 text-white rounded">Tutup</button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Lanjutkan (BARU) --}}
                                    <div x-show="showModal === 'Lanjutkan'" @keydown.escape.window="showModal = false"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                        style="display: none;">
                                        <div @click.away="showModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan Lanjutkan Tugas</h3>
                                            <p class="text-sm text-gray-500 mb-4">Untuk tugas: {{ $task->name }}</p>
                                            <div class="bg-gray-100 p-4 rounded-md">
                                                <p class="text-gray-800">
                                                    {{ $task->activities->where('activity_type', 'lanjutkan_tugas')->last()->notes ?? 'Tidak ada catatan.' }}
                                                </p>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <button @click="showModal = false" class="px-4 py-2 bg-gray-600 text-white rounded">Tutup</button>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                                <td class="text-center py-3 px-4">
                                    <a href="{{ route('dailytasks.upload.form', $task->id) }}" class="text-blue-600 hover:underline">Upload</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Anda belum memiliki tugas yang sedang dikerjakan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>