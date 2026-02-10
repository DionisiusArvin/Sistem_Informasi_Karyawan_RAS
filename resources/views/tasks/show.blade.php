<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col space-y-1">
            <div class="flex items-start">
                <span class="w-33 font-semibold text-xl text-gray-800 dark:text-gray-200">Nama Proyek :&nbsp;</span>
                <span class="text-xl text-gray-800 dark:text-gray-200 flex-1">
                    {{ $task->project->name }}
                </span>
            </div>
            <div class="flex items-start">
                <span class="w-33 font-semibold text-xl text-gray-800 dark:text-gray-200">Tugas Utama :&nbsp;</span>
                <span class="text-xl text-gray-800 dark:text-gray-200 flex-1">
                    @if(($task->jenis_tugas ?? null) === 'Paving')
                        {{ $task->name ? str_replace(' - ', ' ', $task->name) : '' }}
                    @else
                        {{ $task->name ?: $task->jenis_tugas }}
                    @endif
                </span>
            </div>
            @if(($task->jenis_tugas ?? null) !== 'Paving')
                <div class="flex items-start">
                    <span class="w-33 font-semibold text-xl text-gray-800 dark:text-gray-200">Jenis Tugas :&nbsp;</span>
                    <span class="text-xl text-gray-800 dark:text-gray-200 flex-1">
                        {{ $task->jenis_tugas }}
                    </span>
                </div>
            @endif
        </div>
    </x-slot>

    @php
        $dailyTaskOptionsByCategory = [
            'PBG' => [
                'Data Umum' => [
                    'Data Persetujuan Lingkungan (mengikuti peraturan perundangan yang berlaku)',
                    'Data Siteplan yang telah disetujui Pemerintah Daerah Setempat',
                    'Data Penyedia Jasa Perencana',
                    'Data Intensitas Bangunan (KKPR/KRK)',
                    'Data Identitas Pemilik Bangunan (KTP/KITAS)',
                ],
                'Data Teknis Arsitektur' => [
                    'Rekomendasi Peil Banjir',
                    'Spesifikasi Teknis Arsitektur Bangunan',
                    'Gambar Rencana Detail Bangunan',
                    'Gambar Rencana Tampak Bangunan',
                    'Gambar Rencana Potongan Bangunan',
                    'Gambar Rencana Denah Bangunan',
                    'Gambar Rencana Tapak Bangunan',
                    'Gambar Situasi',
                ],
                'Data Teknis Struktur' => [
                    'Spesifikasi Teknis Struktur Bangunan',
                    'Perhitungan Teknis Struktur',
                    'Gambar Rencana Dan Detail Teknis Tangga',
                    'Gambar Rencana Dan Detail Teknis Pelat Lantai',
                    'Gambar Rencana Dan Detail Teknis Penutup',
                    'Gambar Rencana Dan Detail Teknis Rangka Atap',
                    'Gambar Rencana Dan Detail Teknis Balok',
                    'Gambar Rencana Dan Detail Teknis Kolom',
                    'Gambar Rencana Dan Detail Teknis Fondasi dan sloof',
                ],
                'Data Teknis MEP' => [
                    'Spesifikasi Teknis Mekanikal, Elektrikal, dan Plambing',
                    'Perhitungan Teknis Mekanikal, Elektrikal, dan Plambing',
                    'Gambar Rencana Dan Detail Pengelolaan Air Limbah',
                    'Gambar Rencana Dan Detail Pengelolaan Air Bersih',
                    'Gambar Rencana Dan Detail Pencahayaan Umum, dan Pencahanyaan Khusus',
                    'Gambar Rencana Dan Detail Sumber Listrik, dan Jaringan Listrik',
                ],
                'Data Tambahan' => [
                    'Gambar Sederhana Batas Tanah',
                    'Hasil Penyelidikan Tanah',
                    'Peil Banjir',
                ],
                'Upload' => [
                    'Upload semua dokumen ke sistem',
                ],
            ],
            'SLF' => [
                'Data Umum' => [
                    'Data Penyedia Jasa Pengkaji Teknis',
                    'Laporan Pemeriksaan Kelaikan Fungsi Bangunan',
                    'Surat Pernyataan Kelaikan Fungsi',
                    'Data Intensitas Bangunan (KKPR/KRK)',
                    'Data Identitas Pemilik Bangunan (KTP/KITAS)',
                ],
                'Data Teknis Arsitektur' => [
                    'Rekomendasi Peil Banjir',
                    'Gambar Detail Bangunan',
                    'Gambar Tampak Bangunan',
                    'Gambar Potongan Bangunan',
                    'Gambar Denah Bangunan',
                    'Gambar Tapak Bangunan',
                    'Spesifikasi Teknis Arsitektur Bangunan',
                    'Gambar Situasi',
                ],
                'Data Teknis Struktur' => [
                    'Gambar Dan Detail Teknis Penutup',
                    'Gambar Dan Detail Teknis Rangka Atap',
                    'Gambar Dan Detail Teknis Balok',
                    'Gambar Dan Detail Teknis Kolom',
                    'Gambar Dan Detail Teknis Fondasi dan sloof',
                    'Spesifikasi Teknis Struktur Bangunan',
                    'Perhitungan Teknis Struktur',
                ],
                'Data Teknis MEP' => [
                    'Gambar Dan Detail Pengelolaan Air Limbah',
                    'Gambar Dan Detail Pengelolaan Air Bersih',
                    'Gambar Dan Detail Pencahayaan Umum, dan Pencahanyaan Khusus',
                    'Gambar Dan Detail Sumber Listrik, dan Jaringan Listrik',
                    'Spesifikasi Teknis Mekanikal, Elektrikal, dan Plambing',
                    'Perhitungan Teknis Mekanikal, Elektrikal, dan Plambing',
                ],
                'Upload' => [
                    'Upload semua dokumen ke sistem',
                ],
            ],
            'PERENCANAAN' => [
                'Paving' => [
                    'Survey' => [
                        'Survey Lapangan',
                    ],
                    'Gambar Kerja' => [
                        'Layout Eksisting',
                        'Detail Eksisting',
                        'Layout Rencana',
                        'Detail Rencana',
                        'Detail Potongan Rencana',
                    ],
                    'Engineering Estimate' => [
                        'Pembuatan Item RAB',
                        'Perhitungan Volume',
                        'Pembuatan Analisa',
                        'Penentuan Harga Bahan',
                        'Setting RAB sesuai Pagu',
                        'Time Schedule',
                    ],
                    'BOQ' => [
                        'Setting BOQ dari Engineering Estimate',
                    ],
                    'Rencana Kerja dan Syarat2 Teknis' => [
                        'Pembuatan Check List RKS',
                        'Pembuatan RKS dari database yang ada',
                        'Pembuatan RKS baru',
                        'Spesifikasi Teknis',
                    ],
                    'Dokumen Teknis' => [
                        'Rencana Kerja & Syarat-syarat Teknis',
                        'Spesifikasi Teknis',
                        'Metodologi Pelaksanaan Pekerjaan',
                        'Pembuatan SMKK Konsultan',
                        'Pembuatan SMKK Kontaktor',
                    ],
                    'Harga Perkiraan Sendiri' => [
                        'Pembuatan HPS',
                    ],
                    'Laporan' => [
                        'Pembuatan Laporan Pendahuluan',
                        'Pembuatan Laporan Prarencana',
                        'Pembuatan Laporan Antara',
                        'Pembuatan Laporan Akhir',
                    ],
                    'Finalisasi Dokumen Perencanaan' => [
                        'Print dokumen gambar',
                        'Print dokumen EE',
                    ],
                ],
            ],
        ];
        $templateCategory = $task->project->category ?? null;
        if ($templateCategory === 'PERENCANAAN' && ($task->jenis_tugas ?? null) === 'Paving') {
            $pavingMainTasks = [
                'Survey',
                'Gambar Kerja',
                'Engineering Estimate',
                'BOQ',
                'Rencana Kerja dan Syarat2 Teknis',
                'Dokumen Teknis',
                'Harga Perkiraan Sendiri',
                'Laporan',
                'Finalisasi Dokumen Perencanaan',
            ];
            $taskName = trim((string) ($task->name ?? ''));
            $baseTaskName = $taskName;
            $sortedBases = collect($pavingMainTasks)->sortByDesc(fn ($item) => strlen($item))->values();
            foreach ($sortedBases as $base) {
                if (\Illuminate\Support\Str::startsWith(\Illuminate\Support\Str::lower($taskName), \Illuminate\Support\Str::lower($base))) {
                    $baseTaskName = $base;
                    break;
                }
            }
            $dailyTaskOptions = $dailyTaskOptionsByCategory['PERENCANAAN']['Paving'][$baseTaskName] ?? [];
        } else {
            $dailyTaskOptions = $dailyTaskOptionsByCategory[$templateCategory][$task->jenis_tugas] ?? [];
        }
        $showTemplate = !empty($dailyTaskOptions);
    @endphp

    <div class="py-5" x-data="{ showForm: false }">
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
                            <div class="space-y-4">
                                
                                {{-- BARIS 1: [Nama Template - Lebar] [Bobot - Kecil] [Batas Waktu - Kecil] --}}
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <div class="md:col-span-4">
                                        <x-input-label for="name" value="Nama Tugas Harian (Template)" />
                                        @if($showTemplate)
                                            <select id="name" name="name" 
                                                class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                                                <option value="" disabled {{ old('name') ? '' : 'selected' }}>Pilih dari template...</option>
                                                @php $isGrouped = !array_is_list($dailyTaskOptions); @endphp
                                                @if($isGrouped)
                                                    @foreach($dailyTaskOptions as $group => $items)
                                                        <optgroup label="{{ $group }}">
                                                            @foreach($items as $item)
                                                                <option value="{{ $item }}" {{ old('name') === $item ? 'selected' : '' }}>{{ $item }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                @else
                                                    @foreach($dailyTaskOptions as $option)
                                                        <option value="{{ $option }}" {{ old('name') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @else
                                            <select name="project_item_id" id="project_item_id" required 
                                                class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                                                <option value="">Pilih Item Pekerjaan</option>
                                                @foreach($task->project->checklists as $checklist)
                                                    <optgroup label="{{ $checklist->name }}">
                                                        @foreach($checklist->items as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="md:col-span-1">
                                        <x-input-label for="weight" value="Bobot" />
                                        <x-text-input id="weight" class="block mt-1 w-full dark:bg-gray-900 text-sm" type="number" name="weight" min="1" max="10" :value="old('weight', 1)" required />
                                    </div>
                                    <div class="md:col-span-1">
                                        <x-input-label for="due_date" value="Batas Waktu" />
                                        <x-text-input id="due_date" class="block mt-1 w-full dark:bg-gray-900 dark:[color-scheme:dark] text-sm" type="date" name="due_date" :value="old('due_date')" required />
                                    </div>
                                </div>

                                {{-- BARIS 2: [Nama Manual - Lebar] [Tugaskan ke - Kecil/Press] --}}
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <div class="md:col-span-4">
                                        {{-- Label tetap md:text-left agar rapi di kiri --}}
                                        <x-input-label for="manual_name" value="Nama Tugas Harian (Manual)" class="text-left" />
                                        
                                        <x-text-input id="manual_name" 
                                            class="block mt-1 w-full dark:bg-gray-900 text-sm" {{-- Hapus text-center di sini --}}
                                            type="text" name="manual_name" 
                                            :value="old('manual_name')" 
                                            placeholder="Isi manual..."/>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <x-input-label for="assigned_to_staff_id" value="Tugaskan ke" class="text-center md:text-left" />
                                        
                                        <select name="assigned_to_staff_id" id="assigned_to_staff_id" 
                                            class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm text-center appearance-none"
                                            style="text-align-last: center;"> {{-- Bagian ini tetap di tengah --}}
                                            
                                            <option value="" class="text-left">-- Semua Staff Bisa Ambil --</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" class="text-left">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- BARIS 3: Deskripsi --}}
                                <div>
                                    <x-input-label for="description" value="Deskripsi Tugas" />
                                    <textarea id="description" name="description" rows="2" 
                                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        >{{ old('description') }}</textarea>
                                </div>

                                {{-- BARIS 4: Tombol Simpan --}}
                                <div class="flex items-center justify-end pt-2">
                                    <x-primary-button class="!bg-blue-600 hover:!bg-blue-700 active:!bg-blue-800 focus:!ring-blue-500 !text-white border-none shadow-sm px-6">
                                        <i class="fas fa-save mr-2 text-white"></i> 
                                        <span class="text-white font-bold">SIMPAN TUGAS</span>
                                    </x-primary-button>
                                </div>
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
                                    <th scope="col" class="w-2/5 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tugas</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bobot</th>
                                    <th scope="col" class="w-1/5 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pekerja</th>
                                    <th scope="col" class="w-1/5 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progress</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File/Link</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($task->dailyTasks->sortByDesc('created_at') as $dailyTask)
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
                                            <div class="text-sm text-gray-500 mt-1">{{ $dailyTask->description }}</div>
                                            @php
                                                $lastUpload = $dailyTask->activities->where('activity_type', 'upload_pekerjaan')->last();
                                            @endphp
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ $dailyTask->weight ?? '-' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $dailyTask->assignedToStaff->name ?? 'Belum Diambil' }}
                                        </td>

                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-800 dark:text-white font-semibold tabular-nums">
                                                    {{ $dailyTask->fresh()->progress }}%
                                                </span>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-blue-600 h-2.5 rounded-full"
                                                        style="width: {{ $dailyTask->fresh()->progress }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($lastUpload)
                                                <div class="flex items-center space-x-4">
                                                    @if($lastUpload->file_path)
                                                        <a href="{{ asset('storage/' . $lastUpload->file_path) }}" target="_blank" class="font-medium text-green-600 hover:text-green-800">Lihat File</a>
                                                        @if($lastUpload && $lastUpload->file_path)
                                                            <a href="{{ route('dailytasks.download', $dailyTask->id) }}"

                                                            class="font-medium text-indigo-600 hover:text-indigo-800">
                                                            Download File
                                                            </a>
                                                        @endif
                                                    @endif
                                                    @if($lastUpload->link_url)
                                                        <a href="{{ $lastUpload->link_url }}" target="_blank" class="font-medium text-blue-600 hover:text-blue-800">Lihat Link</a>
                                                    @endif
                                                    @if($lastUpload && $lastUpload->notes)
                                                        <div class="mt-1 text-xs text-gray-500 italic">Catatan: "{{ $lastUpload->notes }}"</div>
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
                                                @elseif($dailyTask->status === 'Lanjutkan') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
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
                                                        
                                                        @if(auth()->user()->role === 'staff')

                                                            {{-- Jika tugas belum diklaim --}}
                                                            @if(is_null($dailyTask->assigned_to_staff_id))
                                                                <form action="{{ route('dailytasks.take', $dailyTask->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="text-sm px-4 text-green-600 hover:underline font-semibold">
                                                                        Ambil Tugas
                                                                    </button>
                                                                </form>

                                                            {{-- Jika status revisi --}}
                                                            @elseif($dailyTask->status === 'Revisi')
                                                                <a href="{{ route('dailytasks.upload.form', $dailyTask->id) }}"
                                                                    class="text-blue-600 dark:text-blue-400 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                    Upload Pekerjaan
                                                                </a>

                                                            @elseif($dailyTask->status === 'Lanjutkan')
                                                                <a href="{{ route('dailytasks.upload.form', $dailyTask->id) }}"
                                                                    class="text-blue-600 dark:text-blue-400 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                    Upload Pekerjaan
                                                                </a>

                                                            {{-- Jika sudah diklaim oleh staff yang bersangkutan --}}
                                                            @elseif($dailyTask->assigned_to_staff_id === auth()->id())
                                                                <a href="{{ route('dailytasks.upload.form', $dailyTask->id) }}"
                                                                    class="text-blue-600 dark:text-blue-400 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                    Upload Pekerjaan
                                                                </a>
                                                            @endif

                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- MODALS --}}
                                            {{-- Revision Modal --}}
                                            <div x-show="showRevisionModal" @keydown.escape.window="showRevisionModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                                <div @click.away="showRevisionModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Catatan Revisi untuk: {{ $dailyTask->name }}</h3>
                                                        <form action="{{ route('validation.reject', $dailyTask->id) }}" method="POST">
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
                                            <div x-show="showUploadModal"
                                                @keydown.escape.window="showUploadModal = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                                style="display: none;">

                                                <div @click.away="showUploadModal = false"
                                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">

                                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                                        Kerjakan & Upload: {{ $dailyTask->name }}
                                                    </h3>

                                                    {{-- PENTING: route claim_and_upload + csrf --}}
                                                        <form action="{{ route('dailytasks.upload.handle', $dailyTask->id) }}"
                                                        method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf

                                                        <div class="space-y-4">

                                                            <div>
                                                                <x-input-label for="link_url_{{ $dailyTask->id }}" value="Cantumkan Link (Wajib)" />
                                                                <x-text-input
                                                                    id="link_url_{{ $dailyTask->id }}"
                                                                    class="block mt-1 w-full"
                                                                    type="url"
                                                                    name="link_url"
                                                                    placeholder="https://..."
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <x-input-label for="file_{{ $dailyTask->id }}" value="Upload File (Opsional)" />
                                                                <input
                                                                    id="file_{{ $dailyTask->id }}"
                                                                    class="block w-full text-sm text-gray-500
                                                                        file:mr-4 file:py-2 file:px-4
                                                                        file:rounded-full file:border-0
                                                                        file:font-semibold file:bg-blue-50
                                                                        file:text-blue-700 hover:file:bg-blue-100"
                                                                    type="file"
                                                                    name="file">
                                                            </div>

                                                            <div>
                                                                <x-input-label for="progress_percent_{{ $dailyTask->id }}" value="Progres (%)" />
                                                                <x-text-input
                                                                    id="progress_percent_{{ $dailyTask->id }}"
                                                                    class="block mt-1 w-full"
                                                                    type="number"
                                                                    name="progress_percent"
                                                                    min="0"
                                                                    max="100"
                                                                    step="1"
                                                                    required
                                                                />
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                    Jika deadline lebih dari 1 hari, progres wajib diisi setiap hari.
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <x-input-label for="notes_{{ $dailyTask->id }}" value="Catatan (Opsional)" />
                                                                <textarea
                                                                    name="notes"
                                                                    id="notes_{{ $dailyTask->id }}"
                                                                    rows="3"
                                                                    class="w-full border-gray-300 dark:border-gray-700
                                                                        dark:bg-gray-900 dark:text-gray-300
                                                                        rounded-md shadow-sm"
                                                                    placeholder="Catatan..."></textarea>
                                                            </div>

                                                        </div>

                                                        <div class="mt-6 flex justify-end space-x-2">
                                                            <button type="button"
                                                                    @click="showUploadModal = false"
                                                                    class="px-4 py-2 bg-gray-500 dark:bg-gray-700 text-white rounded-md hover:bg-gray-600 transition duration-150">
                                                                Batal
                                                            </button>

                                                            <button type="submit"
                                                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150">
                                                                Kirim Pekerjaan
                                                            </button>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                            {{-- Edit Modal --}}
                                            <div x-show="showEditModal"
                                                @keydown.escape.window="showEditModal = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                                style="display: none;">

                                                <div @click.away="showEditModal = false"
                                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">

                                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                                        Edit Tugas Harian
                                                    </h3>

                                                    <form action="{{ route('dailytasks.update', $dailyTask->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <div class="space-y-4">
                                                            {{-- ITEM PEKERJAAN / NAMA TUGAS --}}
                                                            <div>
                                                                @if($showTemplate)
                                                                    <x-input-label for="edit_name_{{ $dailyTask->id }}" value="Nama Tugas Harian" />
                                                                    <select id="edit_name_{{ $dailyTask->id }}" name="name"
                                                                        class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                                        <option value="" disabled {{ old('name', $dailyTask->name) ? '' : 'selected' }}>Pilih tugas harian</option>
                                                                        @php
                                                                            $isGrouped = !array_is_list($dailyTaskOptions);
                                                                            $flatTemplateOptions = $isGrouped
                                                                                ? collect($dailyTaskOptions)->flatten(1)->values()->all()
                                                                                : $dailyTaskOptions;
                                                                            $isManualName = !in_array($dailyTask->name, $flatTemplateOptions, true);
                                                                        @endphp
                                                                        @if($isGrouped)
                                                                            @foreach($dailyTaskOptions as $group => $items)
                                                                                <optgroup label="{{ $group }}" class="dark:bg-gray-800">
                                                                                    @foreach($items as $item)
                                                                                        <option value="{{ $item }}" {{ old('name', $dailyTask->name) === $item ? 'selected' : '' }}>{{ $item }}</option>
                                                                                    @endforeach
                                                                                </optgroup>
                                                                            @endforeach
                                                                        @else
                                                                            @foreach($dailyTaskOptions as $option)
                                                                                <option value="{{ $option }}" {{ old('name', $dailyTask->name) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>

                                                                    <div class="mt-3">
                                                                        <x-input-label for="edit_manual_name_{{ $dailyTask->id }}" value="Nama Tugas Harian (Manual)" />
                                                                        {{-- Menambahkan dark:bg-gray-900 agar sinkron --}}
                                                                        <x-text-input id="edit_manual_name_{{ $dailyTask->id }}" 
                                                                            class="block mt-1 w-full dark:bg-gray-900" 
                                                                            type="text" name="manual_name"
                                                                            value="{{ old('manual_name', $isManualName ? $dailyTask->name : '') }}" />
                                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Isi jika tidak ada di template.</p>
                                                                    </div>
                                                                @else
                                                                    <x-input-label value="Item Pekerjaan" />
                                                                    <select name="project_item_id" required
                                                                        class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-700 rounded-md shadow-sm">
                                                                        @foreach($task->project->checklists as $checklist)
                                                                            <optgroup label="{{ $checklist->name }}" class="dark:bg-gray-800">
                                                                                @foreach($checklist->items as $item)
                                                                                    <option value="{{ $item->id }}" {{ $dailyTask->project_item_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                        @endforeach
                                                                    </select>
                                                                @endif
                                                            </div>

                                                            {{-- DUE DATE --}}
                                                            <div>
                                                                <x-input-label value="Batas Waktu" />
                                                                <input type="date"
                                                                    name="due_date"
                                                                    value="{{ \Carbon\Carbon::parse($dailyTask->due_date)->format('Y-m-d') }}"
                                                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm dark:[color-scheme:dark]"
                                                                    required>
                                                            </div>

                                                            {{-- BOBOT --}}
                                                            <div>
                                                                <x-input-label value="Bobot (1-10)" />
                                                                <input type="number"
                                                                    name="weight"
                                                                    value="{{ old('weight', $dailyTask->weight ?? 1) }}"
                                                                    min="1" max="10"
                                                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm"
                                                                    required>
                                                            </div>

                                                            {{-- DESKRIPSI --}}
                                                            <div>
                                                                <x-input-label value="Deskripsi" />
                                                                <textarea name="description"
                                                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm"
                                                                    rows="3">{{ $dailyTask->description }}</textarea>
                                                            </div>

                                                            {{-- ASSIGN --}}
                                                            <div>
                                                                <x-input-label value="Tugaskan ke (Opsional)" />
                                                                <select name="assigned_to_staff_id"
                                                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                                                                    <option value="">-- Semua Staff Bisa Ambil --</option>
                                                                    @foreach ($users as $user)
                                                                        <option value="{{ $user->id }}" {{ $dailyTask->assigned_to_staff_id == $user->id ? 'selected' : '' }}>
                                                                            {{ $user->name }} ({{ $user->role }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- BUTTONS --}}
                                                        <div class="mt-6 flex justify-end space-x-2">
                                                            <button type="button" @click="showEditModal = false"
                                                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                                                                Batal
                                                            </button>
                                                            <button type="submit"
                                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-md">
                                                                Simpan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                            Belum ada tugas harian yang ditambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
