<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Tugas Mendadak
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <form action="{{ route('ad-hoc-tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Nama Tugas --}}
                    <div class="mb-4">
                        <label class="block text-gray-700">Nama Tugas</label>
                        <input type="text" name="name" value="{{ old('name', $task->name) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label class="block text-gray-700">Deskripsi</label>
                        <textarea name="description" class="w-full border rounded-lg px-3 py-2">{{ old('descrition', $task->description) }}</textarea>
                    </div>

                    {{-- Deadline --}}
                    <div class="mb-4">
                        <label class="block text-gray-700">Deadline</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $task->due_date) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    {{-- Ditugaskan Kepada --}}
                    <div class="mb-4">
                        <label class="block text-gray-700">Ditugaskan Kepada</label>
                        <select name="assigned_to_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $task->assigned_to_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="block text-gray-700">Status</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2">
                            <option value="Proses" {{ $task->status === 'Proses' ? 'selected' : '' }}>Proses</option>
                            <option value="Selesai" {{ $task->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
