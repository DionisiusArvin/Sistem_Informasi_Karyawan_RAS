<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Upload Pekerjaan:
            {{ ($adHocTask ?? $task)->name }}
        </h2>
    </x-slot>

    @php
        // SUPPORT DUA VARIABEL (TIDAK ADA YANG DIHAPUS)
        $taskData = $adHocTask ?? $task;
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-sm">

                <form
                    method="POST"
                    action="{{ route('ad-hoc-tasks.upload.handle', $taskData->id) }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    {{-- UPLOAD FILE (SUPPORT file & file_path) --}}
                    <div>
                        <x-input-label for="file" value="Upload File" />
                        <input
                            id="file"
                            {{-- Perbaikan: Border disesuaikan agar sama dengan bagian Link --}}
                            class="block mt-1 w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            type="file"
                            name="file"
                            accept=".pdf,.jpg,.jpeg,.png,.zip,.rar,.docx,.xlsx"
                        />

                        {{-- fallback untuk versi lama --}}
                        <input
                            type="file"
                            name="file_path"
                            class="hidden"
                        />

                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        <x-input-error :messages="$errors->get('file_path')" class="mt-2" />
                    </div>

                    {{-- LINK (SUPPORT link_url & link) --}}
                    <div class="mt-4">
                        <x-input-label for="link_url" value="Link" />
                        <x-text-input
                            id="link_url"
                            class="block mt-1 w-full"
                            type="url"
                            name="link_url"
                            :value="old('link_url', old('link'))"
                            placeholder="https://contoh.com"
                        />
                        <input type="hidden" name="link" value="{{ old('link') }}">

                        <x-input-error :messages="$errors->get('link_url')" class="mt-2" />
                        <x-input-error :messages="$errors->get('link')" class="mt-2" />
                    </div>

                    {{-- CATATAN --}}
                    <div class="mt-4">
                        <x-input-label for="notes" value="Catatan (Opsional)" />
                        <textarea
                            name="notes"
                            id="notes"
                            rows="4"
                            {{-- Perbaikan: Border disesuaikan agar sama dengan bagian Link --}}
                            class="block mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end mt-6">
                        {{-- Tombol Biru Sesuai Permintaan --}}
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Selesaikan Tugas
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>