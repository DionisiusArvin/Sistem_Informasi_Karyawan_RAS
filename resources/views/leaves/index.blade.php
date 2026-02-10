<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6" x-data="{ showFilters: false }">
            {{-- Bagian Header: Judul dan Tombol Aksi Utama --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 sm:mb-0">
                    Daftar Cuti
                </h2>
                <div class="flex items-center space-x-2">
                    {{-- Tombol Tampilkan/Sembunyikan Filter --}}
                    @if(auth()->user()->role == 'manager')
                        <button @click="showFilters = !showFilters" 
                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg shadow-sm transition">
                            <i class="fas fa-filter mr-2"></i>
                            <span x-show="!showFilters">Tampilkan Filter</span>
                            <span x-show="showFilters" style="display: none;">Sembunyikan Filter</span>
                        </button>
                    @endif

                    {{-- Tombol Ajukan Cuti --}}
                    @if(in_array(auth()->user()->role, ['staff','kepala_divisi','admin']))
                        <a href="{{ route('leaves.create') }}" 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                            + Ajukan Cuti
                        </a>
                    @endif
                </div>
            </div>

            {{-- Form Filter --}}
            <div x-show="showFilters" x-transition class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('leaves.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                    
                    {{-- Tanggal Mulai --}}
                    <div>
                        <label for="start_date" class="text-sm font-medium text-gray-700 dark:text-gray-300">Dari</label>
                        {{-- Penambahan [color-scheme:dark] untuk memperbaiki ikon kalender --}}
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:[color-scheme:dark]">
                    </div>

                    {{-- Tanggal Akhir --}}
                    <div>
                        <label for="end_date" class="text-sm font-medium text-gray-700 dark:text-gray-300">Sampai</label>
                        {{-- Penambahan [color-scheme:dark] untuk memperbaiki ikon kalender --}}
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:[color-scheme:dark]">
                    </div>

                    {{-- Divisi --}}
                    <div>
                        <label for="division_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Divisi</label>
                        <select name="division_id" id="division_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">-- Semua Divisi --</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nama User --}}
                    <div>
                        <label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                        <input type="text" name="name" id="name" value="{{ request('name') }}"
                               placeholder="Cari nama..."
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="flex items-end space-x-2">
                        <button type="submit" 
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Divisi</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jenis</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Alasan</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-[140px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($leaves as $leave)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ $leave->user->name }}</td>
                                <td class="px-3 py-2 text-center whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ $leave->division->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-center whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-center whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ ucfirst($leave->type) }}</td>
                                <td class="px-3 py-2 text-sm text-gray-800 dark:text-gray-100 max-w-[250px] break-words whitespace-normal" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    @if($leave->status == 'pending')
                                        <span class="px-1 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>
                                    @elseif($leave->status == 'approved')
                                        <span class="px-1 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Disetujui</span>
                                    @else
                                        <span class="px-1 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm flex gap-2 w-[140px]">
                                    @if(auth()->user()->role == 'manager' && $leave->status == 'pending')
                                        <form action="{{ route('leaves.approve', $leave->id) }}" method="POST">
                                            @csrf @method('PATCH') 
                                            <button type="submit" class="text-green-600 hover:text-green-900">Setujui</button>
                                        </form>
                                        <form action="{{ route('leaves.reject', $leave->id) }}" method="POST">
                                            @csrf @method('PATCH') 
                                            <button type="submit" class="text-red-600 hover:text-red-900">Tolak</button>
                                        </form>
                                    @else
                                        <span class="px-11 py-3 text-center whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-500">Tidak ada data cuti yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>