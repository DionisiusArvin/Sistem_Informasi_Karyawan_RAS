<x-app-layout>
    <x-slot name="header">
        <span>
            Buat Proyek Baru
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Form untuk membuat proyek baru --}}
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf
                        
                        <div>
                            <x-input-label for="name" value="Nama Proyek" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="kode_proyek" value="Kode Proyek (Opsional)" />
                            <x-text-input id="kode_proyek" class="block mt-1 w-full" type="text" name="kode_proyek" :value="old('kode_proyek', $project->kode_proyek ?? '')" />
                            <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="client_name" value="Nama Klien" />
                            <x-text-input id="client_name" class="block mt-1 w-full" type="text" name="client_name" :value="old('client_name')" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="start_date" value="Tanggal Mulai" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="end_date" value="Tanggal Selesai" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
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