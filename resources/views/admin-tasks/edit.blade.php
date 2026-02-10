<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Tugas Admin: ') }} {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Box Container dengan Support Dark Mode --}}
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-sm border border-transparent dark:border-gray-700">
                
                <form method="POST" action="{{ route('admin-tasks.update', $task->id) }}">
                    @csrf
                    @method('PATCH')

                    {{-- Nama Tugas --}}
                    <div>
                        <x-input-label for="name" value="Nama Tugas" />
                        <x-text-input id="name" 
                            class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500" 
                            type="text" name="name" :value="old('name', $task->name)" required />
                    </div>

                    {{-- Deskripsi Tugas --}}
                    <div class="mt-4">
                        <x-input-label for="description" value="Deskripsi Tugas" />
                        <textarea name="description" id="description" 
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            rows="4">{{ old('description', $task->description) }}</textarea>
                    </div>

                    {{-- Batas Waktu (Date) --}}
                    <div class="mt-4">
                        <x-input-label for="due_date" value="Batas Waktu (Opsional)" />
                        {{-- Menggunakan [color-scheme] agar ikon kalender tidak hitam di dark mode --}}
                        <x-text-input id="due_date" 
                            class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500 [color-scheme:light] dark:[color-scheme:dark]" 
                            type="date" name="due_date" :value="old('due_date', $task->due_date)" />
                    </div>

                    {{-- Tugaskan ke Admin --}}
                    <div class="mt-4">
                        <x-input-label for="assigned_to_admin_id" value="Tugaskan ke Admin" />
                        <select name="assigned_to_admin_id" id="assigned_to_admin_id" 
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}" {{ (old('assigned_to_admin_id', $task->assigned_to_admin_id) == $admin->id) ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Simpan (Biru) --}}
                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>