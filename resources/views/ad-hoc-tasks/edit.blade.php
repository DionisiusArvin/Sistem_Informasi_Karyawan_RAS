<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Tugas Mendadak
        </h2>
    </x-slot>

    @php
        // SUPPORT DUA VARIABEL: $adHocTask & $task (TIDAK ADA YANG DIHAPUS)
        $taskData = $adHocTask ?? $task;
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">

                {{-- ERROR VALIDATION (dari versi 1) --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
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
                        <label class="block text-gray-800 dark:text-gray-200">Nama Tugas</label>
                        <input type="text" name="name"
                            value="{{ old('name', $taskData->name) }}"
                            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-900 text-gray-800 dark:text-gray-200"
                            required>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label class="block text-gray-800 dark:text-gray-200">Deskripsi</label>
                        <textarea name="description"
                            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('description', $taskData->description) }}</textarea>
                    </div>

                    {{-- Deadline --}}
                    <div class="mb-4">
                        <label class="block text-gray-800 dark:text-gray-200">Deadline</label>
                        <input type="date" name="due_date"
                            value="{{ old('due_date', $taskData->due_date) }}"
                            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                    </div>

                    {{-- Ditugaskan Kepada --}}
                    <div class="mb-4">
                        <label class="block text-gray-800 dark:text-gray-200">Ditugaskan Kepada</label>
                        <select name="assigned_to_id"
                            class="w-full border rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200"
                            required>
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('assigned_to_id', $taskData->assigned_to_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status (DIGABUNG SEMUA, TIDAK DIHAPUS) --}}
                    <div class="mb-6">
                        <label class="block text-gray-800 dark:text-gray-200">Status</label>
                        <select name="status"
                            class="w-full border rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200"
                            required>

                            @php
                                $statuses = [
                                    'Belum Dikerjakan',
                                    'Menunggu Validasi',
                                    'Proses',
                                    'Selesai'
                                ];
                            @endphp

                            @foreach($statuses as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $taskData->status) === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
