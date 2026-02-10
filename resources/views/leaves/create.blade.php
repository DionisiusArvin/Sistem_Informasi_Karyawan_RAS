<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Ajukan Cuti</h2>

        {{-- Notifikasi error --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg border border-red-200 dark:border-red-800">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('leaves.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Divisi --}}
            @if(auth()->user()->role != 'admin')
                <input type="hidden" name="division_id" value="{{ auth()->user()->division_id }}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Divisi</label>
                    <input type="text" 
                        value="{{ auth()->user()->division->name }}" 
                        disabled
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-900 dark:text-gray-400 cursor-not-allowed">
                </div>
            @endif


            {{-- Tanggal mulai --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" 
                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-lg shadow-sm dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 dark:[color-scheme:dark]"
                       min="{{ date('Y-m-d') }}"
                       value="{{ old('start_date') }}" required>
            </div>

            {{-- Tanggal selesai --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date" 
                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-lg shadow-sm dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 dark:[color-scheme:dark]"
                       min="{{ date('Y-m-d') }}"
                       value="{{ old('end_date') }}" required>
            </div>

            {{-- Jenis cuti --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis</label>
                <select name="type" id="type" 
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-lg shadow-sm dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="" class="dark:bg-gray-900">-- Pilih Jenis --</option>
                    <option value="sakit" {{ old('type') == 'sakit' ? 'selected' : '' }} class="dark:bg-gray-900">Sakit</option>
                    <option value="izin" {{ old('type') == 'izin' ? 'selected' : '' }} class="dark:bg-gray-900">Izin</option>
                </select>
            </div>

            {{-- Alasan --}}
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan</label>
                <textarea name="reason" id="reason" rows="3" 
                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-lg shadow-sm dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                          required>{{ old('reason') }}</textarea>
            </div>

            <div class="flex justify-end pt-2">
                <a href="{{ route('leaves.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition font-medium shadow-sm">Batal</a>
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition font-medium shadow-sm">
                    Ajukan
                </button>
            </div>
        </form>
    </div>

    {{-- JS untuk sinkronisasi tanggal --}}
    <!-- Toast Notification -->
    <div
        x-data="{ show: false, message: '', type: 'success', timeoutId: null }"
        x-show="show"
        x-cloak
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-2 opacity-0"
        @notify.window="
            message = $event.detail.message;
            type = $event.detail.type || 'success';
            show = true;
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => show = false, 3500);
        "
        class="fixed top-6 right-6 z-50 max-w-sm"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-start gap-3 rounded-xl border border-gray-200/70 dark:border-gray-700/60 bg-white/90 dark:bg-gray-900/90 backdrop-blur px-4 py-3 shadow-xl ring-1 ring-black/5">
            <div
                class="mt-0.5 h-8 w-8 rounded-full flex items-center justify-center"
                :class="type === 'success'
                    ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300'
                    : 'bg-rose-500/10 text-rose-600 dark:text-rose-300'"
            >
                <svg x-show="type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="type !== 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="type === 'success' ? 'Berhasil' : 'Terjadi Kesalahan'"></p>
                <p class="text-sm text-gray-600 dark:text-gray-300" x-text="message"></p>
            </div>

            <button
                type="button"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                @click="show = false"
                aria-label="Tutup notifikasi"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

<script>
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');

    function notify(msg, type = 'success') {
        window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: type } }));
    }

    startInput.addEventListener('change', () => {
        endInput.min = startInput.value;

        if (endInput.value < startInput.value) {
            notify("Tanggal selesai tidak boleh sebelum tanggal mulai!", "error");
            endInput.value = startInput.value;
        }
    });

    endInput.addEventListener('change', () => {
        if (endInput.value < startInput.value) {
            notify("Tanggal selesai tidak boleh sebelum tanggal mulai!", "error");
            endInput.value = startInput.value;
        }
    });
</script>

<!-- Tambahkan di halaman Blade setelah redirect sukses -->
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            notify("{{ session('success') }}", "success");
        });
    </script>
@endif
@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            notify("{{ session('error') }}", "error");
        });
    </script>
@endif


</x-app-layout>

