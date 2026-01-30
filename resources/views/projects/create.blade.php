<x-app-layout>
    <x-slot name="header">
        <span>
            Buat Proyek Baru
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Form untuk membuat proyek baru --}}
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf
                        
                        {{-- Nama Proyek --}}
                        <div>
                            <x-input-label for="name" value="Nama Proyek" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        </div>

                        {{-- Kode Proyek --}}
                        <div class="mt-4">
                            <x-input-label for="kode_proyek" value="Kode Proyek (Opsional)" />
                            <x-text-input id="kode_proyek" class="block mt-1 w-full" type="text" name="kode_proyek" :value="old('kode_proyek', $project->kode_proyek ?? '')" />
                            <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                        </div>

                        {{-- Nama Klien --}}
                        <div class="mt-4">
                            <x-input-label for="client_name" value="Nama Klien" />
                            <x-text-input id="client_name" class="block mt-1 w-full" type="text" name="client_name" :value="old('client_name')" required />
                        </div>

                        {{-- Tanggal Mulai --}}
                        <div class="mt-4">
                            <x-input-label for="start_date" value="Tanggal Mulai" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" required />
                        </div>

                        {{-- Tanggal Selesai --}}
                        <div class="mt-4">
                            <x-input-label for="end_date" value="Tanggal Selesai" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" required />
                        </div>
                        
                        {{-- Kategori --}}
                        <div class="mt-4">
                            <x-input-label for="category" value="Kategori" />
                            <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
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
                            <x-text-input id="contract_value" class="block mt-1 w-full" type="number" min="0" name="contract_value" :value="old('contract_value')" placeholder="Contoh: 50000000" />
                            <x-input-error :messages="$errors->get('contract_value')" class="mt-2" />
                        </div>

                        {{-- PIC / Penanggung Jawab --}}
                        <div class="mt-4">
                            <x-input-label for="pic_id" value="PIC / Penanggung Jawab" />
                            <select id="pic_id" name="pic_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Pilih Penanggung Jawab --</option>
                                @foreach($picUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('pic_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ ucfirst($user->role) }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('pic_id')" class="mt-2" />
                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                Simpan Proyek
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
