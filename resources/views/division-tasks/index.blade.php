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
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="w-1/2 text-left py-3 px-4 uppercase font-semibold text-sm">Nama Tugas</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Batas Waktu</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        {{-- PERBAIKAN 1: Gunakan variabel $availableTasks --}}
                        @forelse ($availableTasks as $task)
                            <tr>
                                <td class="w-1/2 py-3 px-4">{{ $task->name }}</td>
                                <td class="py-3 px-4">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <form action="{{ route('dailytasks.claim', $task->id) }}" method="POST">
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

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tugas Saya</h3>
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="w-1/2 text-left py-3 px-4 uppercase font-semibold text-sm">Nama Tugas</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Batas Waktu</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Progress</th> {{-- Kolom Baru --}}
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($myTasks as $task)
                            <tr x-data="{ showModal: false }">
                                <td class="w-1/2 py-3 px-4">{{ $task->name }}</td>
                                <td class="py-3 px-4">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        {{-- Tambahkan span ini untuk menampilkan angka --}}
                                        <span class="mr-2 text-sm">{{ $task->status_based_progress }}%</span>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $task->status_based_progress }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-semibold">
                                    @if ($task->status === 'Selesai')
                                        <span class="text-green-600">
                                            Selesai
                                            <span class="font-normal italic">
                                                ({{ $task->completion_status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }})
                                            </span>
                                        </span>
                                    @elseif($task->status === 'Revisi')
                                        <button @click="showModal = true" class="font-semibold text-red-600 hover:underline">
                                            Revisi (Lihat Catatan)
                                        </button>
                                    @else
                                        {{ $task->status }}
                                    @endif
                                    
                                    {{-- PERBAIKAN 2: Pindahkan Modal ke dalam <td> agar HTML valid --}}
                                    @if($task->status === 'Revisi')
                                    <div x-show="showModal" @keydown.escape.window="showModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="showModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan Revisi dari Kepala Divisi</h3>
                                            <p class="text-sm text-gray-500 mb-4">Untuk tugas: {{ $task->name }}</p>
                                            <div class="bg-gray-100 p-4 rounded-md">
                                                <p class="text-gray-800">{{ $task->activities->where('activity_type', 'permintaan_revisi')->last()->notes ?? 'Tidak ada catatan.' }}</p>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-600 text-white rounded">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
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
</x-app-layout>