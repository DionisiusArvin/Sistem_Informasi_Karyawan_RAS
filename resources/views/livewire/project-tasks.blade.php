<table class="min-w-full bg-white dark:bg-gray-800">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="w-1/12"></th> <!-- Kolom ikon drag -->
            <th class="w-2/6 text-center py-4 px-2 uppercase font-semibold text-sm text-gray-600 dark:text-gray-200">Tugas & Deskripsi</th>
            <th class="w-1/5 text-center py-4 px-2 uppercase font-semibold text-sm text-gray-600 dark:text-gray-200">Divisi</th>
            <th class="w-1/6 text-center py-4 px-2 uppercase font-semibold text-sm text-gray-600 dark:text-gray-200">Status Tugas</th>
            <th class="w-1/5 text-center py-4 px-2 uppercase font-semibold text-sm text-gray-600 dark:text-gray-200">Progress</th>
            <th class="w-1/6 text-center py-4 px-2 uppercase font-semibold text-sm text-gray-600 dark:text-gray-200">Aksi</th>
        </tr>
    </thead>

    <tbody id="sortableTasks" class="divide-y divide-gray-200 dark:divide-gray-700">

        @forelse ($project->tasks as $task)

            @php
                $belum = $task->dailyTasks()->whereIn('status', ['Belum Diambil', 'Belum Dikerjakan'])->count();
                $selesai = $task->dailyTasks()->where('status', 'Selesai')->count();
                $menunggu = $task->dailyTasks()->where('status', 'Menunggu Validasi')->count();
                $revisi = $task->dailyTasks()->where('status', 'Revisi')->count();
            @endphp

            <tr data-id="{{ $task->id }}">
                {{-- IKON DRAG (hanya kepala_divisi yang melihat) --}}
                <td class="py-4 px-4 text-center">
                    @if(auth()->user()->role === 'kepala_divisi')
                        <div class="drag-handle text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 8h16M4 16h16" />
                            </svg>
                        </div>
                    @endif
                </td>

                {{-- Kolom Tugas --}}
                <td class="py-4 px-4">
                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $task->name }}</p>
                    @if($task->description)
                        <p class="text-sm dark:text-gray-200 text-gray-500 mt-1 italic">{{ $task->description }}</p>
                    @endif
                </td>

                {{-- Kolom Divisi --}}
                <td class="py-4 px-4">
                    <div class="flex flex-wrap gap-1 justify-center">
                        @foreach($task->divisions as $division)
                            <span class="px-2 py-1 text-center text-xs font-semibold leading-tight rounded-full bg-gray-100 text-gray-700">
                                {{ $division->name }}
                            </span>
                        @endforeach
                    </div>
                </td>

                {{-- Kolom Status --}}
                <td class="py-4 px-4 text-center">
                    <div class="flex justify-center items-center space-x-3">
                        <div class="w-6 h-6 flex items-center justify-center rounded-full bg-green-500 text-white text-xs font-bold">
                            {{ $selesai }}
                        </div>
                        <div class="w-6 h-6 flex items-center justify-center rounded-full bg-yellow-400 text-white text-xs font-bold">
                            {{ $menunggu }}
                        </div>
                        <div class="w-6 h-6 flex items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold">
                            {{ $revisi }}
                        </div>
                        <div class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-500 text-white text-xs font-bold">
                            {{ $belum }}
                        </div>
                    </div>
                </td>

                {{-- Kolom Progress --}}
                <td class="py-4 px-4">
                    <div class="flex items-center">
                        <span class="mr-2 text-sm text-gray-800 dark:text-gray-200">{{ $task->getProgressPercentage() }}%</span>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full"
                                style="width: {{ $task->getProgressPercentage() }}%">
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Kolom Aksi --}}
                <td class="py-4 px-4">
                    <div class="flex flex-col items-center gap-2">

                        <a href="{{ route('tasks.show', $task->id) }}"
                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm w-24 text-center">
                            Detail
                        </a>

                        @if(auth()->user()->role === 'kepala_divisi')
                            <a href="{{ route('tasks.edit', $task->id) }}"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm w-24 text-center">
                                Edit
                            </a>

                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus tugas ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm w-24">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="5" class="text-center py-6 text-gray-500">Belum ada tugas utama.</td>
            </tr>
        @endforelse

    </tbody>
</table>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let table = document.getElementById('sortableTasks');

    Sortable.create(table, {
        animation: 150,
        handle: '.drag-handle', // hanya bisa drag dari ikon
        onEnd: function () {
            let order = [];
            document.querySelectorAll('#sortableTasks tr').forEach((row, index) => {
                order.push({ id: row.getAttribute('data-id'), order: index });
            });

            fetch("{{ route('tasks.reorder') }}", {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ order: order })
            });
        }
    });
});
</script>

