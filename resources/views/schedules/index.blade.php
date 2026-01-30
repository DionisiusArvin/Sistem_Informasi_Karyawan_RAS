<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Jadwal Kegiatan Kepala Divisi
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Button Tambah --}}
            <button onclick="openModal()"
                class="mb-4 bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
                + Tambah Jadwal
            </button>

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm uppercase">
                            <th class="py-3 px-4 text-left">Kegiatan</th>
                            <th class="py-3 px-4 text-center">Tanggal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y dark:divide-gray-700">
                        @foreach($schedules as $sch)
                            <tr>
                                <td class="py-3 px-4">
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $sch->name }}</p>
                                    @if($sch->description)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $sch->description }}
                                        </p>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-center text-gray-800 dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($sch->date)->format('d M Y') }}
                                </td>

                                <td class="py-3 px-4 text-center">
                                    @if($sch->status == 'pending')
                                        <span class="px-3 py-1 bg-yellow-400 text-white rounded-full text-xs">
                                            Pending
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-green-600 text-white rounded-full text-xs">
                                            Selesai
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-center">

                                    {{-- Tombol Selesai untuk hari ini --}}
                                    @if($sch->date == $today && $sch->status == 'pending')
                                        <form method="POST" action="{{ route('schedules.done', $sch->id) }}" class="inline">
                                            @csrf
                                            <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                                Selesai
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Tombol Edit --}}
                                    @if($sch->status == 'pending')
                                        <button 
                                            onclick="openEditModal('{{ $sch->id }}', '{{ $sch->name }}', '{{ $sch->date }}', `{{ $sch->description }}`)"
                                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm ml-2">
                                            Edit
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if($schedules->count() == 0)
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada jadwal.
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- MODAL TAMBAH JADWAL --}}
    <div id="modalAdd" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white dark:bg-gray-800 w-96 p-6 rounded-lg shadow">

            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                Tambah Jadwal Baru
            </h3>

            <form method="POST" action="{{ route('schedules.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="text-gray-700 dark:text-gray-300">Judul</label>
                    <input type="text" name="name" required
                        class="w-full mt-1 rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div class="mb-3">
                    <label class="text-gray-700 dark:text-gray-300">Tanggal</label>
                    <input type="date" name="date" required
                        class="w-full mt-1 rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div class="mb-3">
                    <label class="text-gray-700 dark:text-gray-300">Deskripsi (opsional)</label>
                    <textarea name="description"
                        class="w-full mt-1 rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-gray-700 dark:text-gray-300"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-600">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
    {{-- MODAL EDIT JADWAL --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white dark:bg-gray-800 w-96 p-6 rounded-lg shadow">

            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                Edit Jadwal
            </h3>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="text-gray-700 dark:text-gray-300">Judul</label>
                    <input id="edit_name" type="text" name="name" required
                        class="w-full mt-1 rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div class="mb-3">
                    <label class="text-gray-700 dark:text-gray-300">Tanggal</label>
                    <input id="edit_date" type="date" name="date" required
                        class="w-full mt-1 rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div class="mb-3">
                    <label class="text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea id="edit_description" name="description"
                        class="w-full mt-1 rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-gray-700 dark:text-gray-300"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-600">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2 rounded bg-yellow-600 text-white hover:bg-yellow-700">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>


    <script>
        function openModal() {
            document.getElementById('modalAdd').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('modalAdd').classList.add('hidden');
        }
        function openEditModal(id, name, date, description) {

            // Set value input
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_description').value = description ?? '';

            // Set action route
            document.getElementById('editForm').action = '/schedules/' + id;

            // Show modal
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('modalEdit').classList.add('hidden');
        }
    </script>

</x-app-layout>
