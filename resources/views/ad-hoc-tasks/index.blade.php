<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Tugas Mendadak
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">Tugas Mendadak</h3>
                    @if(auth()->user()->role === 'manager' || auth()->user()->role === 'kepala_divisi')
                        <a href="{{ route('ad-hoc-tasks.create') }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            + Tambah Tugas
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                            <tr>
                                <th class="px-6 py-3 text-center">Nama Tugas</th>
                                <th class="px-6 py-3 text-center">Deskripsi</th>
                                <th class="px-6 py-3 text-center">Deadline</th>
                                <th class="px-6 py-3 text-center">Ditugaskan Kepada</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-600 divide-y divide-gray-200">
                            @forelse($tasks as $task)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $task->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $task->description }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ $task->assignedTo->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $task->status === 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $task->status }}
                                        </span>
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch(auth()->user()->role)

                                            {{-- STAFF --}}
                                            @case('staff')
                                                @if(
                                                    ($task->assigned_to_id === auth()->id()) && 
                                                    (auth()->user()->role === 'staff' || auth()->user()->role === 'kepala_divisi') && 
                                                    $task->status !== 'Selesai'
                                                )
                                                    <a href="{{ route('ad-hoc-tasks.upload', $task->id) }}"
                                                    class="inline-block px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                                        Upload
                                                    </a>
                                                @endif
                                            @break

                                            {{-- MANAGER & KEPALA DIVISI --}}
                                            @case('manager')
                                            @case('kepala_divisi')
                                                <div class="flex flex-wrap items-center gap-2">
                                                    @if(($task->assigned_to_id === auth()->id()) && 
                                                    (auth()->user()->role === 'kepala_divisi') && $task->status !== 'Selesai')
                                                    <a href="{{ route('ad-hoc-tasks.upload', $task->id) }}"
                                                    class="inline-block px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                                        Upload
                                                    </a>
                                                    @endif
                                                    {{-- File --}}
                                                    @if($task->file_path)
                                                        <a href="{{ route('ad-hoc-tasks.downloadFile', $task->id) }}"
                                                           class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                                            Download
                                                        </a>
                                                    @endif

                                                    {{-- Link --}}
                                                    @if($task->link)
                                                        <a href="{{ $task->link }}" target="_blank"
                                                           class="px-3 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 text-sm">
                                                            Lihat Link
                                                        </a>
                                                    @endif

                                                    {{-- Notes --}}
                                                    @if($task->notes)
                                                        <span class="text-gray-500 italic">Catatan: {{ $task->notes }}</span>
                                                    @endif

                                                    {{-- Edit --}}
                                                    <a href="{{ route('ad-hoc-tasks.edit', $task->id) }}"
                                                       class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                                                        Edit
                                                    </a>

                                                    {{-- Hapus --}}
                                                    <form action="{{ route('ad-hoc-tasks.destroy', $task->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Yakin ingin menghapus tugas ini?');"
                                                          class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @break

                                            {{-- DEFAULT --}}
                                            @default
                                                <span class="text-gray-400">Tidak ada aksi</span>
                                        @endswitch
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada tugas mendadak.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
