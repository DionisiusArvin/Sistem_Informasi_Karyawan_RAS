<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Ajukan Cuti</h2>

        {{-- Notifikasi error --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <ul class="list-disc pl-5">
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
                    <label class="block text-sm font-medium text-gray-700">Divisi</label>
                    <input type="text" 
                        value="{{ auth()->user()->division->name }}" 
                        disabled
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
                </div>
            @endif


            {{-- Tanggal mulai --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" 
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm"
                       min="{{ date('Y-m-d') }}"
                       value="{{ old('start_date') }}" required>
            </div>

            {{-- Tanggal selesai --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date" 
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm"
                       min="{{ date('Y-m-d') }}"
                       value="{{ old('end_date') }}" required>
            </div>

            {{-- Jenis cuti --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis</label>
                <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="sakit" {{ old('type') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ old('type') == 'izin' ? 'selected' : '' }}>Izin</option>
                </select>
            </div>

            {{-- Alasan --}}
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan</label>
                <textarea name="reason" id="reason" rows="3" 
                          class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm"
                          required>{{ old('reason') }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('leaves.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2">Batal</a>
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    Ajukan
                </button>
            </div>
        </form>
    </div>

    {{-- JS untuk sinkronisasi tanggal --}}
    <!-- Toast Container (Reusable) -->
<div 
    x-data="{ show: false, message: '', type: 'success' }" 
    x-show="show"
    x-transition
    x-cloak
    @notify.window="
        message = $event.detail.message; 
        type = $event.detail.type; 
        show = true; 
        setTimeout(() => show = false, 3000)
    "
    class="fixed top-5 right-5 px-4 py-2 rounded-lg shadow-lg text-sm"
    :class="type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
>
    <span x-text="message"></span>
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
            notify("⚠️ Tanggal selesai tidak boleh sebelum tanggal mulai!", "error");
            endInput.value = startInput.value;
        }
    });

    endInput.addEventListener('change', () => {
        if (endInput.value < startInput.value) {
            notify("⚠️ Tanggal selesai tidak boleh sebelum tanggal mulai!", "error");
            endInput.value = startInput.value;
        }
    });
</script>

<!-- Tambahkan di halaman Blade setelah redirect sukses -->
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            notify("✅ {{ session('success') }}", "success");
        });
    </script>
@endif
@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            notify("❌ {{ session('error') }}", "error");
        });
    </script>
@endif


</x-app-layout>
