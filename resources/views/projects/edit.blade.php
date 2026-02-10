<x-app-layout>
    <x-slot name="header">
        <span>
            Edit Proyek: {{ $project->name }}
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-sm border border-transparent dark:border-gray-700">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('projects.update', $project->id) }}">
                        @csrf
                        @method('PUT')
                        
                        {{-- Nama Proyek --}}
                        <div>
                            <x-input-label for="name" value="Nama Proyek" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name', $project->name)" required autofocus />
                        </div>

                        {{-- Kode Proyek --}}
                        <div class="mt-4">
                            <x-input-label for="kode_proyek" value="Kode Proyek (Opsional)" />
                            <x-text-input id="kode_proyek" class="block mt-1 w-full" type="text" name="kode_proyek"
                                :value="old('kode_proyek', $project->kode_proyek ?? '')" />
                            <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                        </div>

                        {{-- Klien --}}
                        <div class="mt-4">
                            <x-input-label for="client_name" value="Nama Klien" />
                            <x-text-input id="client_name" class="block mt-1 w-full" type="text" name="client_name"
                                :value="old('client_name', $project->client_name)" required />
                        </div>

                        {{-- Tanggal Mulai --}}
                        <div class="mt-4">
                            <x-input-label for="start_date" value="Tanggal Mulai" />
                            <x-text-input id="start_date" class="block mt-1 w-full dark:[color-scheme:dark]" type="date" name="start_date"
                                :value="old('start_date', $project->start_date)" required />
                        </div>

                        {{-- Tanggal Selesai --}}
                        <div class="mt-4">
                            <x-input-label for="end_date" value="Tanggal Selesai" />
                            <x-text-input id="end_date" class="block mt-1 w-full dark:[color-scheme:dark]" type="date" name="end_date"
                                :value="old('end_date', $project->end_date)" required />
                        </div>

                        {{-- Kategori --}}
                        <div class="mt-4">
                            <x-input-label for="category" value="Kategori" />
                            <select id="category" name="category" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach (['PBG', 'SLF', 'PBG dan SLF', 'PERENCANAAN', 'PENGAWASAN', 'KONSULTASI'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $project->category) == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        {{-- Nilai Kontrak --}}
                        <div class="mt-4">
                            <x-input-label for="contract_value" value="Nilai Kontrak (Rp)" />
                            <x-text-input id="contract_value" class="block mt-1 w-full" type="number" min="0"
                                name="contract_value" :value="old('contract_value', $project->contract_value)" placeholder="Masukkan nilai kontrak" />
                        </div>

                        {{-- PIC --}}
                        <div class="mt-4">
                            <x-input-label for="pic_id" value="PIC / Penanggung Jawab Lapangan" />
                            <select id="pic_id" name="pic_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Penanggung Jawab --</option>
                                @foreach($users as $user) {{-- Pastikan variabel $users dikirim dari Controller --}}
                                    <option value="{{ $user->id }}" {{ old('pic_id', $project->pic_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('pic_id')" class="mt-2" />
                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
