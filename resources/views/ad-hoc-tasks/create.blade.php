<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Buat Tugas Mendadak Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-sm">
                <form method="POST" action="{{ route('ad-hoc-tasks.store') }}">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nama Tugas" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                    </div>

                    <div class="mt-4 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                        <x-input-label for="description" value="Deskripsi Tugas (Opsional)" />
                        {{-- Menambahkan border-gray-300 dark:border-gray-700 --}}
                        <textarea name="description" id="description" class="block mt-1 w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 rounded-md shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="assigned_to_id" value="Tugaskan ke" />
                        {{-- Menambahkan border-gray-300 dark:border-gray-700 --}}
                        <select name="assigned_to_id" id="assigned_to_id" class="block mt-1 w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 rounded-md shadow-sm" required>
                            <option selected disabled>Pilih Karyawan</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="due_date" value="Batas Waktu (Opsional)" />
                        <x-text-input id="due_date" 
                            class="block mt-1 w-full dark:bg-gray-900 dark:[color-scheme:dark]" 
                            type="date" 
                            name="due_date" 
                            :value="old('due_date')" />
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Simpan Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>