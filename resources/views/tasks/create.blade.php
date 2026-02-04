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
                        
                        <div>
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

                        <div class="mt-4">
                            <x-input-label for="name" value="Nama Tugas (Opsional)" />
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
</x-app-layout>
