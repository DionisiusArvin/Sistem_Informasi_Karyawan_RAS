<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tambah Tugas Baru untuk Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-sm">

                <form method="POST" action="{{ route('admin-tasks.store') }}">
                    @csrf

                    {{-- Nama Tugas --}}
                    <div>
                        <x-input-label for="name" value="Nama Tugas" />
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            class="block mt-1 w-full"
                            :value="old('name')"
                            required
                        />
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mt-4">
                        <x-input-label for="description" value="Deskripsi Tugas" />
                        <textarea
                            id="description"
                            name="description"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- Batas Waktu (Date) --}}
                    <div class="mt-4">
                        <x-input-label for="due_date" value="Batas Waktu (Opsional)" />
                        {{-- Menambahkan dark:[color-scheme:dark] dan background gelap --}}
                        <x-text-input
                            id="due_date"
                            name="due_date"
                            type="date"
                            class="block mt-1 w-full dark:bg-gray-900 dark:[color-scheme:dark]"
                            :value="old('due_date')"
                        />
                    </div>

                    {{-- Proyek --}}
                    <div class="mt-4">
                        <x-input-label for="project_id" value="Kaitkan ke Proyek (Opsional)" />
                        <select
                            id="project_id"
                            name="project_id"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Tidak ada</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Admin --}}
                    <div class="mt-4">
                        <x-input-label for="assigned_to_admin_id" value="Tugaskan ke Admin" />
                        <select
                            id="assigned_to_admin_id"
                            name="assigned_to_admin_id"
                            required
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="" disabled selected>Pilih Admin</option>
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}"
                                    {{ old('assigned_to_admin_id') == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Submit Button (Warna Biru) --}}
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