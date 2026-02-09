<x-app-layout>
    <x-slot name="header">
        <span>Tambah Tugas Baru untuk Admin</span>
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
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- Due Date --}}
                    <div class="mt-4">
                        <x-input-label for="due_date" value="Batas Waktu (Opsional)" />
                        <x-text-input
                            id="due_date"
                            name="due_date"
                            type="date"
                            class="block mt-1 w-full"
                            :value="old('due_date')"
                        />
                    </div>

                    {{-- Proyek --}}
                    <div class="mt-4">
                        <x-input-label for="project_id" value="Kaitkan ke Proyek (Opsional)" />
                        <select
                            id="project_id"
                            name="project_id"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
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
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
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

                    {{-- Submit --}}
                    <div class="flex justify-end mt-6">
                        <x-primary-button>
                            Simpan Tugas
                        </x-primary-button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
