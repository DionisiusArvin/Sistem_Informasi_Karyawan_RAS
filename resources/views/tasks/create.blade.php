<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Tugas Baru untuk Proyek: {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('projects.tasks.store', $project->id) }}">
                        @csrf
                        
                        <div>
                            <x-input-label for="name" value="Nama Tugas" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" value="Deskripsi (Opsional)" />
                            <textarea name="description" id="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
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