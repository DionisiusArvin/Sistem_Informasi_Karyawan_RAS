<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Upload Pekerjaan: {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- PERBAIKAN: Menambahkan dark:bg-gray-800 dan border dark --}}
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-sm border dark:border-gray-700">
                <form method="POST" action="{{ route('admin-tasks.upload.handle', $task->id) }}" enctype="multipart/form-data">
                    @csrf

                    {{-- LINK --}}
                    <div>
                        <x-input-label for="link" value="Link" class="dark:text-gray-300" />
                        <x-text-input id="link" class="block mt-1 w-full" type="url" name="link" :value="old('link')" placeholder="https://..." required />
                        <x-input-error :messages="$errors->get('link')" class="mt-2" />
                    </div>

                    {{-- UPLOAD FILE --}}
                    <div class="mt-4">
                        <x-input-label for="file" value="Upload File" class="dark:text-gray-300" />
                        <input id="file" 
                            {{-- PERBAIKAN: Border disesuaikan dengan dark mode --}}
                            class="block mt-1 w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                            type="file" name="file" required />
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    {{-- CATATAN --}}
                    <div class="mt-4">
                        <x-input-label for="notes" value="Catatan (Opsional)" class="dark:text-gray-300" />
                        <textarea name="notes" id="notes" 
                            {{-- PERBAIKAN: Border dan warna teks --}}
                            class="block mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500"
                            rows="4">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end mt-6">
                        {{-- PERBAIKAN: Button warna biru --}}
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                            Upload Pekerjaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>