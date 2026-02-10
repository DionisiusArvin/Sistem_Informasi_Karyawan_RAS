<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Proyek Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Box Container dengan Border Dark Mode --}}
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-sm border border-transparent dark:border-gray-700">
                
                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf
                    
                    {{-- Nama Proyek --}}
                    <div>
                        <x-input-label for="name" value="Nama Proyek" />
                        <x-text-input id="name" class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500" type="text" name="name" :value="old('name')" required autofocus />
                    </div>

                    {{-- Kode Proyek --}}
                    <div class="mt-4">
                        <x-input-label for="kode_proyek" value="Kode Proyek (Opsional)" />
                        <x-text-input id="kode_proyek" class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500" type="text" name="kode_proyek" :value="old('kode_proyek')" />
                        <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                    </div>

                    {{-- Nama Klien --}}
                    <div class="mt-4">
                        <x-input-label for="client_name" value="Nama Klien" />
                        <x-text-input id="client_name" class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500" type="text" name="client_name" :value="old('client_name')" required />
                    </div>

                    {{-- Tanggal Mulai (Satu Baris Penuh) --}}
                    <div class="mt-4">
                        <x-input-label for="start_date" value="Tanggal Mulai" />
                        <x-text-input id="start_date" 
                            class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500 [color-scheme:light] dark:[color-scheme:dark]" 
                            type="date" name="start_date" required />
                    </div>

                    {{-- Tanggal Selesai (Satu Baris Penuh) --}}
                    <div class="mt-4">
                        <x-input-label for="end_date" value="Tanggal Selesai" />
                        <x-text-input id="end_date" 
                            class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500 [color-scheme:light] dark:[color-scheme:dark]" 
                            type="date" name="end_date" required />
                    </div>
                    
                    {{-- Kategori --}}
                    <div class="mt-4">
                        <x-input-label for="category" value="Kategori" />
                        <select id="category" name="category" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="PBG">PBG</option>
                            <option value="SLF">SLF</option>
                            <option value="PBG dan SLF">PBG dan SLF</option>
                            <option value="PERENCANAAN">PERENCANAAN</option>
                            <option value="PENGAWASAN">PENGAWASAN</option>
                            <option value="KONSULTASI">KONSULTASI</option>
                        </select>
                    </div>

                    {{-- Nilai Kontrak --}}
                    <div class="mt-4">
                        <x-input-label for="contract_value" value="Nilai Kontrak (Rp)" />
                        <x-text-input id="contract_value" class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500" type="number" min="0" name="contract_value" :value="old('contract_value')" placeholder="Contoh: 50000000" />
                        <x-input-error :messages="$errors->get('contract_value')" class="mt-2" />
                    </div>

                    {{-- PIC / Penanggung Jawab --}}
                    <div class="mt-4">
                        <x-input-label for="pic_id" value="PIC / Penanggung Jawab" />
                        <select id="pic_id" name="pic_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="">-- Pilih Penanggung Jawab --</option>
                            @foreach($picUsers as $user)
                                <option value="{{ $user->id }}" {{ old('pic_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('pic_id')" class="mt-2" />
                    </div>

                    {{-- Tombol Simpan (Biru) --}}
                    <div class="flex items-center justify-end mt-6">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm">
                            Simpan Proyek
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>