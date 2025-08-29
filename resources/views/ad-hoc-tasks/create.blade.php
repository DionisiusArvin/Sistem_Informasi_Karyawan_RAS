<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Tugas Mendadak Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <form method="POST" action="{{ route('ad-hoc-tasks.store') }}">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nama Tugas" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="description" value="Deskripsi Tugas (Opsional)" />
                        <textarea name="description" id="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="assigned_to_id" value="Tugaskan ke" />
                        <select name="assigned_to_id" id="assigned_to_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            <option selected disabled>Pilih Karyawan</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="due_date" value="Batas Waktu (Opsional)" />
                        <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date')" />
                    </div>
                    <div class="flex justify-end mt-6">
                        <x-primary-button>Simpan Tugas</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>