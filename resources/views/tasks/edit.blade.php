<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Tugas: ') }} {{ ($task->jenis_tugas ?? null) === 'Paving' ? str_replace(' - ', ' ', ($task->name ?? '')) : ($task->name ?: $task->jenis_tugas) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800 dark:text-gray-200">
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Jenis Tugas --}}
                        <div>
                            <x-input-label for="jenis_tugas" value="Jenis Tugas" />
                            @php
                                $jenisTugasOptionsByCategory = [
                                    'PBG' => ['Data Umum', 'Data Teknis Arsitektur', 'Data Teknis Struktur', 'Data Teknis MEP', 'Data Tambahan', 'Upload'],
                                    'SLF' => ['Data Umum', 'Data Teknis Arsitektur', 'Data Teknis Struktur', 'Data Teknis MEP', 'Upload'],
                                    'PERENCANAAN' => ['Paving', 'Rigid', 'Taman', 'Makam'],
                                ];
                                $jenisTugasOptions = $jenisTugasOptionsByCategory[$task->project->category ?? ''] ?? null;
                            @endphp

                            @if($jenisTugasOptions)
                                <select name="jenis_tugas" id="jenis_tugas" 
                                    class="block mt-1 w-full dark:bg-gray-900 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="" disabled>-- Pilih Jenis Tugas --</option>
                                    @foreach($jenisTugasOptions as $option)
                                        <option value="{{ $option }}" {{ old('jenis_tugas', $task->jenis_tugas) === $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <x-text-input class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" type="text" value="{{ $task->jenis_tugas }}" disabled />
                                <input type="hidden" name="jenis_tugas" value="{{ old('jenis_tugas', $task->jenis_tugas) }}">
                            @endif
                        </div>

                        {{-- Nama Tugas --}}
                        <div class="mt-4">
                            <x-input-label for="name" value="Judul Tugas Utama (Opsional)" />
                            <x-text-input id="name" name="name" type="text" 
                                class="block mt-1 w-full dark:bg-gray-900" 
                                value="{{ old('name', $task->name) }}" />
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mt-4">
                            <x-input-label for="description" value="Deskripsi" />
                            <textarea name="description" id="description" rows="3"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('description', $task->description) }}</textarea>
                        </div>

                        {{-- Checkbox Divisi (Dibuat List Vertikal sesuai Gambar) --}}
                        @can('update-task-division')
                            <div class="mt-4">
                                <x-input-label value="Pilih Divisi yang Berkolaborasi" />
                                <div class="mt-2 space-y-2"> {{-- Ini yang membuat jadi list ke bawah --}}
                                    @foreach(App\Models\Division::all() as $division)
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="divisions[]" value="{{ $division->id }}"
                                                {{ $task->divisions->contains($division->id) ? 'checked' : '' }}
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ $division->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endcan

                        {{-- Tombol Simpan --}}
                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" 
                                class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>