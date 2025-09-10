<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Pekerjaan: {{ $task->name }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <form method="POST" action="{{ route('ad-hoc-tasks.upload.handle', $task->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <x-input-label for="file_path" value="Upload File" />
                        <input id="file_path" class="block mt-1 w-full" type="file" name="file_path" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="link" value="Link (Opsional)" />
                        <x-text-input id="link" class="block mt-1 w-full" type="url" name="link" :value="old('link')" placeholder="https://..." required />
                        <x-input-error :messages="$errors->get('link')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="notes" value="Catatan (Opsional)" />
                        <textarea name="notes" id="notes" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-primary-button>Selesaikan Tugas</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
