<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Upload Pekerjaan: {{ $dailyTask->name }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <form method="POST" action="{{ route('dailytasks.upload.handle', $dailyTask->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Silakan cantumkan link pekerjaan Anda. Upload file bersifat opsional.
                        </p>

                        <div>
                            <x-input-label for="link_url" value="Cantumkan Link (Wajib)" />
                            <x-text-input id="link_url" class="block mt-1 w-full" type="url" name="link_url" :value="old('link_url')" placeholder="https://..." required />
                            <x-input-error :messages="$errors->get('link_url')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="file" value="Upload File Tambahan (Opsional)" />
                            <input id="file" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" type="file" name="file" />
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="notes" value="Catatan (Opsional)" />
                            <textarea name="notes" id="notes" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/50 flex justify-end">
                        <x-primary-button>Kirim untuk Validasi</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>