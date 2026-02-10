<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Tugas Mendadak') }}
        </h2>
    </x-slot>

    @php
        // SUPPORT DUA VARIABEL: $adHocTask & $task (TIDAK ADA YANG DIHAPUS)
        $taskData = $adHocTask ?? $task;
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Container dengan background & border dark mode --}}
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-sm border border-transparent dark:border-gray-700">

                {{-- ERROR VALIDATION --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg border border-red-200 dark:border-red-800">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form 
                    action="{{ route('ad-hoc-tasks.update', $taskData->id) }}" 
                    method="POST" 
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')

                    {{-- Nama Tugas --}}
                    <div class="mb-4">
                        <x-input-label for="name" value="Nama Tugas" />
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500" 
                            value="{{ old('name', $taskData->name) }}" 
                            required 
                        />
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('description', $taskData->description) }}</textarea>
                    </div>

                    {{-- Deadline --}}
                    <div class="mb-4">
                        <x-input-label for="due_date" value="Deadline" />
                        {{-- color-scheme:dark agar ikon kalender putih di dark mode --}}
                        <x-text-input 
                            id="due_date" 
                            name="due_date" 
                            type="date" 
                            class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500 [color-scheme:light] dark:[color-scheme:dark]" 
                            value="{{ old('due_date', $taskData->due_date) }}" 
                        />
                    </div>

                    {{-- Ditugaskan Kepada --}}
                    <div class="mb-4">
                        <x-input-label for="assigned_to_id" value="Ditugaskan Kepada" />
                        <select 
                            id="assigned_to_id" 
                            name="assigned_to_id" 
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                            required
                        >
                            <option value="" disabled>-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to_id', $taskData->assigned_to_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="mb-6">
                        <x-input-label for="status" value="Status" />
                        <select 
                            id="status" 
                            name="status" 
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                            required
                        >
                            @php
                                $statuses = ['Belum Dikerjakan', 'Menunggu Validasi', 'Proses', 'Selesai'];
                            @endphp
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ old('status', $taskData->status) === $status ? 'selected' : '' }}>
                                    {{ $status }}
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