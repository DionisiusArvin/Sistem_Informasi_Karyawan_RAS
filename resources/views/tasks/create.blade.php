<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4 md:mb-0">
            Tambah Tugas Baru untuk Proyek: {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800 dark:text-gray-200">
                    <form method="POST" action="{{ route('projects.tasks.store', $project->id) }}">
                        @csrf
                        
                        <div id="jenis-tugas-wrapper">
                            <x-input-label for="jenis_tugas" value="Jenis Tugas" />
                            @php
                                $jenisTugasOptionsByCategory = [
                                    'PBG' => [
                                        'Data Umum',
                                        'Data Teknis Arsitektur',
                                        'Data Teknis Struktur',
                                        'Data Teknis MEP',
                                        'Data Tambahan',
                                        'Upload',
                                    ],
                                    'SLF' => [
                                        'Data Umum',
                                        'Data Teknis Arsitektur',
                                        'Data Teknis Struktur',
                                        'Data Teknis MEP',
                                        'Upload',
                                    ],
                                    'PERENCANAAN' => [
                                        'Paving',
                                        'Rigid',
                                        'Taman',
                                        'Makam',
                                    ],
                                ];
                                $jenisTugasOptions = $jenisTugasOptionsByCategory[$project->category ?? ''] ?? null;
                            @endphp
                            @if($jenisTugasOptions)
                                <select id="jenis_tugas" name="jenis_tugas" class="block mt-1 w-full dark:bg-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled {{ old('jenis_tugas') ? '' : 'selected' }}>Pilih jenis tugas</option>
                                    @foreach($jenisTugasOptions as $option)
                                        <option value="{{ $option }}" {{ old('jenis_tugas') === $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" id="jenis_tugas" name="jenis_tugas" value="{{ $project->category ?? 'Non-PBG' }}">
                            @endif
                        </div>

                        @if(in_array($project->category ?? null, ['PBG', 'SLF']))
                            <div class="mt-4" id="pbg-slf-mode-wrapper">
                                <x-input-label for="pbg_slf_mode" value="Mode Tugas Utama" />
                                <select id="pbg_slf_mode" name="pbg_slf_mode" class="block mt-1 w-full dark:bg-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="auto" {{ old('pbg_slf_mode', 'auto') === 'auto' ? 'selected' : '' }}>Auto-generate semua jenis</option>
                                    <option value="manual" {{ old('pbg_slf_mode') === 'manual' ? 'selected' : '' }}>Input Manual</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Jika auto, sistem akan membuat tugas utama untuk semua jenis.
                                </p>
                            </div>
                        @endif

                        @if(($project->category ?? null) === 'PERENCANAAN')
                            <div class="mt-4 hidden" id="paving-mode-wrapper">
                                <x-input-label for="paving_mode" value="Mode Tugas Utama (Paving)" />
                                <select id="paving_mode" name="paving_mode" class="block mt-1 w-full dark:bg-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="auto" {{ old('paving_mode', 'auto') === 'auto' ? 'selected' : '' }}>Auto-generate</option>
                                    <option value="manual" {{ old('paving_mode') === 'manual' ? 'selected' : '' }}>Input Manual</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Jika auto, sistem akan membuat tugas utama otomatis.
                                </p>
                            </div>
                        @endif

                        <div class="mt-4" id="name-wrapper">
                            <x-input-label for="name" value="Judul Tugas Utama (Opsional)" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" value="Deskripsi (Opsional)" />
                            <textarea name="description" id="description" class="block mt-1 w-full dark:bg-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <x-input-label value="Pilih Divisi yang Berkolaborasi" />
                            <div class="mt-2 space-y-2">
                                @foreach(App\Models\Division::all() as $division)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="divisions[]" value="{{ $division->id }}" class="rounded">
                                        <span class="ml-2">{{ $division->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                Simpan Tugas
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(in_array($project->category ?? null, ['PERENCANAAN', 'PBG', 'SLF']))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const projectCategory = @json($project->category ?? null);
                    const jenisSelect = document.getElementById('jenis_tugas');
                    const jenisWrapper = document.getElementById('jenis-tugas-wrapper');
                    const pbgSlfModeWrapper = document.getElementById('pbg-slf-mode-wrapper');
                    const pbgSlfModeSelect = document.getElementById('pbg_slf_mode');
                    const modeWrapper = document.getElementById('paving-mode-wrapper');
                    const modeSelect = document.getElementById('paving_mode');
                    const nameWrapper = document.getElementById('name-wrapper');
                    const nameInput = document.getElementById('name');

                    function syncMode() {
                        const isPerencanaan = projectCategory === 'PERENCANAAN';
                        const isPbgSlf = projectCategory === 'PBG' || projectCategory === 'SLF';
                        const jenisValue = jenisSelect ? jenisSelect.value : '';
                        const isPaving = isPerencanaan && jenisValue === 'Paving';
                        if (modeWrapper) {
                            modeWrapper.classList.toggle('hidden', !isPaving);
                        }
                        const mode = modeSelect ? modeSelect.value : 'auto';
                        const manual = isPaving && mode === 'manual';
                        const pbgMode = pbgSlfModeSelect ? pbgSlfModeSelect.value : 'auto';
                        const pbgAuto = isPbgSlf && pbgMode === 'auto';

                        if (pbgSlfModeWrapper) {
                            pbgSlfModeWrapper.classList.toggle('hidden', !isPbgSlf);
                        }
                        if (jenisWrapper) {
                            jenisWrapper.classList.toggle('hidden', pbgAuto);
                        }
                        if (jenisSelect) {
                            jenisSelect.required = !pbgAuto;
                            jenisSelect.disabled = pbgAuto;
                        }
                        if (nameInput) {
                            nameInput.required = manual;
                        }
                    }

                    if (jenisSelect) {
                        jenisSelect.addEventListener('change', syncMode);
                    }
                    if (modeSelect) {
                        modeSelect.addEventListener('change', syncMode);
                    }
                    if (pbgSlfModeSelect) {
                        pbgSlfModeSelect.addEventListener('change', syncMode);
                    }
                    syncMode();
                });
            </script>
        @endpush
    @endif
</x-app-layout>
