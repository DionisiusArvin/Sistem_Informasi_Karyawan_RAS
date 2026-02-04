<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-gray-200 text-gray-800 leading-tight">
            Edit Tugas: {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800 dark:text-gray-200">
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm dark:text-gray-200 font-medium">Nama Tugas</label>
                            <input type="text" name="name" value="{{ old('name', $task->name) }}"
                                class="w-full rounded border-gray-300 bg-white dark:bg-gray-900">
                        </div>

                        <div>
                            <label class="block text-sm dark:text-gray-200 font-medium">Deskripsi</label>
                            <textarea name="description" class="w-full rounded border-gray-300 bg-white dark:bg-gray-900">{{ old('description', $task->description) }}</textarea>
                        </div>

                        @can('update-task-division')
                            <div>
                                <label class="text-sm dark:text-gray-200 font-medium">Divisi</label>
                                <div class="mt-2 space-y-2">
                                    @foreach(App\Models\Division::all() as $division)
                                        <label class="flex dark:text-gray-200 items-center">
                                            <input type="checkbox" name="divisions[]" value="{{ $division->id }}"
                                                {{ $task->divisions->contains($division->id) ? 'checked' : '' }}
                                                class="rounded">
                                            <span class="ml-2">{{ $division->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endcan

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
