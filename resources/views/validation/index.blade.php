    <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-200 leading-tight">
            Validasi Tugas Harian Divisi
        </h2>
    </x-slot>

    <div x-data="modalHandler()" class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-center text-lg text-gray-900 dark:text-gray-300">Proyek</th>
                            <th class="px-4 py-2 text-center text-lg text-gray-900 dark:text-gray-300">Tugas Utama</th>
                            <th class="px-4 py-2 text-center text-lg text-gray-900 dark:text-gray-300">Tugas Harian</th>
                            <th class="px-4 py-2 text-center text-lg text-gray-900 dark:text-gray-300">Pegawai</th>
                            <th class="px-4 py-2 text-center text-lg text-gray-900 dark:text-gray-300">File/Link</th>
                            <th class="px-4 py-2 text-center text-lg text-gray-900 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-gray-800 divide-y">
                        @forelse ($tasks as $task)
                            <tr>
                                <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-300">
                                    {{ $task->task->project->name ?? '-' }}
                                </td>

                                <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-300">
                                    {{ $task->task->name ?? '-' }}
                                </td>

                                <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-300">
                                    {{ $task->name }}
                                </td>

                                <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-300">
                                    {{ $task->assignedToStaff->name ?? '-' }}
                                </td>

                                <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-300">
                                    @php $lastUpload = $task->activities->last(); @endphp

                                    @if($lastUpload)
                                        <div class="flex flex-col items-center space-y-1">

                                            @if($lastUpload->file_path)
                                                <a href="{{ url('storage/' . $lastUpload->file_path) }}" target="_blank" class="text-green-600 text-sm">Lihat File</a>
                                                <a href="{{ route('dailytasks.download', $task->id) }}" class="text-indigo-600 text-sm">Download</a>
                                            @endif

                                            @if($lastUpload->link_url)
                                                <a href="{{ $lastUpload->link_url }}" target="_blank" class="text-blue-600 text-sm">Lihat Link</a>
                                            @endif

                                            @if($lastUpload->notes)
                                                <div class="text-xs text-gray-500 italic">
                                                    Catatan: "{{ $lastUpload->notes }}"
                                                </div>
                                            @endif

                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-2 text-center">

                                    <!-- Buka modal lanjut -->
                                    <button 
                                        @click="openModal('lanjut', {{ $task->id }}, '{{ $task->name }}')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm w-full mb-2">
                                        Lanjutkan
                                    </button>

                                    <!-- Buka modal revisi -->
                                    <button 
                                        @click="openModal('revisi', {{ $task->id }}, '{{ $task->name }}')"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm w-full mb-2">
                                        Revisi
                                    </button>

                                    <!-- SETUJUI -->
                                    <form action="{{ route('validation.approve', $task->id) }}" method="POST">
                                        @csrf
                                        <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-sm w-full">
                                            Setujui
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500">
                                    Tidak ada tugas yang perlu divalidasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        <!-- MODAL LANJUTKAN -->
        <div x-show="showLanjut"
            x-transition.opacity.duration.300ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            style="display:none;"
            @keydown.escape.window="showLanjut=false">

            <div @click.away="showLanjut=false"
                x-transition.duration.300ms
                x-transition.scale.origin.center
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md transition-all duration-300 ease-out">

                <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">
                    Lanjutkan Tugas: <span x-text="selectedName"></span>
                </h3>

                <form :action="'/validation/' + selectedId + '/continue'" method="POST">
                    @csrf
                    <textarea name="notes" rows="4"
                        class="w-full border rounded-md p-2 dark:bg-gray-900 dark:text-gray-200"
                        placeholder="Catatan untuk melanjutkan..."></textarea>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" @click="showLanjut=false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded text-gray-900 dark:text-gray-300">
                            Batal
                        </button>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                            Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL REVISI -->
       <div x-show="showRevisi"
            x-transition.opacity.duration.300ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            style="display:none;"
            @keydown.escape.window="showRevisi = false">

            <div @click.away="showRevisi = false"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">

                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Catatan Revisi untuk: <span x-text="selectedName"></span>
                </h3>

                <form :action="`/validation/${selectedId}/reject`" method="POST">
                    @csrf
                    <input type="hidden" name="_method">

                    <textarea name="revision_notes" rows="4"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                            placeholder="Jelaskan bagian yang perlu direvisi..." required></textarea>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" @click="showRevisi = false"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded">
                            Batal
                        </button>

                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
                            Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-app-layout>

<script>
    function modalHandler() {
        return {
            showLanjut: false,
            showRevisi: false,
            selectedId: null,
            selectedName: '',
    
            openModal(type, id, name) {
                this.selectedId = id;
                this.selectedName = name;
    
                if (type === 'lanjut') {
                    this.showLanjut = true;
                }
    
                if (type === 'revisi') {
                    this.showRevisi = true;
                }
            }
        }
    }
</script>
