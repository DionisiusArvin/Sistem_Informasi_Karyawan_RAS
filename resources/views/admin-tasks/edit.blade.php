<x-app-layout>
    <x-slot name="header">
        <span>
            Edit Tugas Admin: {{ $task->name }}
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <form method="POST" action="{{ route('admin-tasks.update', $task->id) }}">
                    @csrf
                    @method('PATCH') {{-- atau PUT --}}

                    <div>
                        <x-input-label for="name" value="Nama Tugas" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $task->name)" required />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="description" value="Deskripsi Tugas" />
                        <textarea name="description" id="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $task->description) }}</textarea>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="due_date" value="Batas Waktu (Opsional)" />
                        <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', $task->due_date)" />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="assigned_to_admin_id" value="Tugaskan ke Admin" />
                        <select name="assigned_to_admin_id" id="assigned_to_admin_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}" @if($admin->id === $task->assigned_to_admin_id) selected @endif>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end mt-6">
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>