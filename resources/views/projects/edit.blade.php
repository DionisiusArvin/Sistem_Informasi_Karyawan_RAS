<x-app-layout>
    <x-slot name="header">
        <span>
            Edit Proyek: {{ $project->name }}
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('projects.update', $project->id) }}">
                        @csrf
                        @method('PUT') {{-- 1. Tambahkan method PUT --}}
                        
                        <div>
                            <x-input-label for="name" value="Nama Proyek" />
                            {{-- 2. Isi value dengan data yang ada --}}
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $project->name)" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="kode_proyek" value="Kode Proyek (Opsional)" />
                            <x-text-input id="kode_proyek" class="block mt-1 w-full" type="text" name="kode_proyek" :value="old('kode_proyek', $project->kode_proyek ?? '')" />
                            <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="client_name" value="Nama Klien" />
                            <x-text-input id="client_name" class="block mt-1 w-full" type="text" name="client_name" :value="old('client_name', $project->client_name)" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="start_date" value="Tanggal Mulai" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $project->start_date)" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="end_date" value="Tanggal Selesai" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date', $project->end_date)" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                Simpan Perubahan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>