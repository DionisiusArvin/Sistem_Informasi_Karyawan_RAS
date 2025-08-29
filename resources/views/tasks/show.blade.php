<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Tugas: {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showForm: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6">
                    {{-- Header Kartu --}}
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Tugas Harian</h3>
                        @if(auth()->user()->role === 'kepala_divisi' && $task->divisions->contains('id', auth()->user()->division_id))
                            <button @click="showForm = !showForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                <span x-show="!showForm"><i class="fas fa-plus mr-2"></i> Tambah Tugas Harian</span>
                                <span x-show="showForm" style="display: none;">Batal</span>
                            </button>
                        @endif
                    </div>

                    {{-- Form Tambah Tugas (Bisa disembunyikan) --}}
                    <div x-show="showForm" x-transition class="border-b dark:border-gray-700 mb-6 pb-6">
                        <form method="POST" action="{{ route('tasks.dailytasks.store', $task->id) }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <x-input-label for="name" value="Nama Tugas Harian" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                </div>
                                <div>
                                    <x-input-label for="due_date" value="Batas Waktu" />
                                    <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date')" required />
                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>Simpan Tugas</x-primary-button>
                            </div>
                        </form>
                    </div>
                    
                    {{-- =================================================================== --}}
                    {{-- AWAL BAGIAN TABEL YANG DIRAPIKAN --}}
                    {{-- =================================================================== --}}
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="w-2/5 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tugas & Catatan</th>
                                    <th scope="col" class="w-1/5 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pekerja</th>
                                    <th scope="col" class="w-1/5 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progress</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File/Link</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($task->dailyTasks as $dailyTask)
                                    <tr x-data="{ 
                                        showRevisionModal: false, 
                                        showUploadModal: false,
                                        showEditModal: false,
                                        actionDropdownOpen: false 
                                    }" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        
                                        <td class="px-6 py-4 whitespace-normal">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $dailyTask->name }}</div>
                                            @php
                                                $lastUpload = $dailyTask->activities->where('activity_type', 'upload_pekerjaan')->last();
                                            @endphp
                                            @if($lastUpload && $lastUpload->notes)
                                                <div class="mt-1 text-xs text-gray-500 italic">Catatan: "{{ $lastUpload->notes }}"</div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $dailyTask->assignedToStaff->name ?? 'Belum Diambil' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-600">
                                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $dailyTask->status_based_progress }}%"></div>
                                                </div>
                                                <span class="ml-3 text-sm font-medium text-gray-500 dark:text-gray-300">{{ $dailyTask->status_based_progress }}%</span>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($lastUpload)
                                                <div class="flex items-center space-x-4">
                                                    @if($lastUpload->file_path)
                                                        <a href="{{ asset('storage/' . $lastUpload->file_path) }}" target="_blank" class="font-medium text-green-600 hover:text-green-800">Lihat File</a>
                                                    @endif
                                                    @if($lastUpload->link_url)
                                                        <a href="{{ $lastUpload->link_url }}" target="_blank" class="font-medium text-blue-600 hover:text-blue-800">Lihat Link</a>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($dailyTask->status === 'Selesai') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($dailyTask->status === 'Menunggu Validasi') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($dailyTask->status === 'Revisi') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                                {{ $dailyTask->status }}
                                                @if ($dailyTask->status === 'Selesai')
                                                    <span class="ml-1 font-normal normal-case">({{ str_replace('_', ' ', $dailyTask->completion_status) }})</span>
                                                @endif
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="relative inline-block text-left">
                                                <div>
                                                    <button @click="actionDropdownOpen = !actionDropdownOpen" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" id="menu-button" aria-expanded="true" aria-haspopup="true">
                                                        Aksi
                                                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div x-show="actionDropdownOpen"
                                                     @click.away="actionDropdownOpen = false"
                                                     x-transition
                                                     class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                                                     role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" style="display: none;">
                                                    <div class="py-1" role="none">
                                                        
                                                        @if(auth()->user()->role === 'kepala_divisi')
                                                            @if(!$dailyTask->assignedToStaff)
                                                                <a href="#" @click.prevent="showUploadModal = true; actionDropdownOpen = false" class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600" role="menuitem" tabindex="-1">Kerjakan Sendiri</a>
                                                            @elseif($dailyTask->status === 'Menunggu Validasi')
                                                                <form action="{{ route('dailytasks.approve', $dailyTask->id) }}" method="POST" class="w-full" role="menuitem" tabindex="-1">
                                                                    @csrf @method('PATCH')
                                                                    <button type="submit" class="w-full text-left text-green-600 dark:text-green-400 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">Approve</button>
                                                                </form>
                                                                <a href="#" @click.prevent="showRevisionModal = true; actionDropdownOpen = false" class="text-red-600 dark:text-red-400 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600" role="menuitem" tabindex="-1">Revisi</a>
                                                            @elseif($dailyTask->assigned_to_staff_id === auth()->id())
                                                                <a href="{{ route('dailytasks.upload.form', $dailyTask->id) }}" class="text-blue-600 dark:text-blue-400 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600" role="menuitem" tabindex="-1">Upload Pekerjaan</a>
                                                            @endif
                                                            
                                                            <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                                            <a href="#" @click.prevent="showEditModal = true; actionDropdownOpen = false" class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600" role="menuitem" tabindex="-1">Edit</a>
                                                            <form action="{{ route('dailytasks.destroy', $dailyTask->id) }}" method="POST" class="w-full" role="menuitem" tabindex="-1">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" onclick="return confirm('Yakin hapus tugas ini?')" class="w-full text-left text-red-600 dark:text-red-400 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">Hapus</button>
                                                            </form>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- MODALS --}}
                                            {{-- Revision Modal --}}
                                            <div x-show="showRevisionModal" @keydown.escape.window="showRevisionModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                                <div @click.away="showRevisionModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Catatan Revisi untuk: {{ $dailyTask->name }}</h3>
                                                    <form action="{{ route('dailytasks.reject', $dailyTask->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <textarea name="revision_notes" rows="4" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" placeholder="Jelaskan bagian yang perlu direvisi..." required></textarea>
                                                        <div class="mt-4 flex justify-end space-x-2">
                                                            <button type="button" @click="showRevisionModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded">Batal</button>
                                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Kirim Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- Upload Modal --}}
                                            <div x-show="showUploadModal" @keydown.escape.window="showUploadModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                                <div @click.away="showUploadModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Kerjakan & Upload: {{ $dailyTask->name }}</h3>
                                                    <form action="{{ route('dailytasks.claim_and_upload', $dailyTask->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="space-y-4">
                                                            <div>
                                                                <x-input-label for="link_url_{{ $dailyTask->id }}" value="Cantumkan Link (Wajib)" />
                                                                <x-text-input id="link_url_{{ $dailyTask->id }}" class="block mt-1 w-full" type="url" name="link_url" placeholder="https://..." required />
                                                            </div>
                                                            <div>
                                                                <x-input-label for="file_{{ $dailyTask->id }}" value="Upload File (Opsional)" />
                                                                <input id="file_{{ $dailyTask->id }}" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" type="file" name="file">
                                                            </div>
                                                            <div>
                                                                <x-input-label for="notes_{{ $dailyTask->id }}" value="Catatan (Opsional)" />
                                                                <textarea name="notes" id="notes_{{ $dailyTask->id }}" rows="3" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" placeholder="Catatan..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="mt-6 flex justify-end space-x-2">
                                                            <button type="button" @click="showUploadModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-md">Batal</button>
                                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Kirim Pekerjaan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- Edit Modal --}}
                                            <div x-show="showEditModal" @keydown.escape.window="showEditModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                                <div @click.away="showEditModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Edit Tugas Harian</h3>
                                                    <form action="{{ route('dailytasks.update', $dailyTask->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <div class="space-y-4">
                                                            <div>
                                                                <x-input-label for="edit_name_{{ $dailyTask->id }}" value="Nama Tugas Harian" />
                                                                <x-text-input id="edit_name_{{ $dailyTask->id }}" class="block mt-1 w-full" type="text" name="name" value="{{ $dailyTask->name }}" required />
                                                            </div>
                                                            <div>
                                                                <x-input-label for="edit_due_date_{{ $dailyTask->id }}" value="Batas Waktu" />
                                                                <x-text-input id="edit_due_date_{{ $dailyTask->id }}" class="block mt-1 w-full" type="date" name="due_date" value="{{ \Carbon\Carbon::parse($dailyTask->due_date)->format('Y-m-d') }}"  required />
                                                            </div>
                                                        </div>
                                                        <div class="mt-6 flex justify-end space-x-2">
                                                            <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-md">Batal</button>
                                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                            Belum ada tugas harian yang ditambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- =================================================================== --}}
                    {{-- AKHIR BAGIAN TABEL YANG DIRAPIKAN --}}
                    {{-- =================================================================== --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>