<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4">

            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Laporan Tugas Admin
            </h2>

            {{-- FILTER --}}
            <form action="{{ route('reports.admin-tasks') }}" method="GET" class="flex flex-wrap items-end gap-3">

                {{-- Filter Admin (manager saja) --}}
                @if(auth()->user()->role === 'manager')
                    <select name="user_id" class="text-sm h-9 border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                        <option value="">Semua Admin</option>
                        @foreach($filterableUsers as $filterableUser)
                            <option value="{{ $filterableUser->id }}" @selected(request('user_id') == $filterableUser->id)>
                                {{ $filterableUser->name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                {{-- MODE --}}
                <select name="mode" id="mode" class="text-sm h-9 border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                    <option value="tanggal" @selected(request('mode')=='tanggal')>Per Tanggal</option>
                    <option value="range" @selected(request('mode')=='range')>Range Tanggal</option>
                    <option value="bulan" @selected(request('mode')=='bulan')>Per Bulan</option>
                    <option value="tahun" @selected(request('mode')=='tahun')>Per Tahun</option>
                </select>

                {{-- TANGGAL --}}
                <input type="date" name="date" id="filter-tanggal"
                       value="{{ request('date') }}"
                       class="text-sm h-9 border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">

                {{-- RANGE --}}
                <div id="filter-range" class="flex gap-2">
                    <input type="date" name="from" value="{{ request('from') }}" class="text-sm h-9 border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                    <input type="date" name="to" value="{{ request('to') }}" class="text-sm h-9 border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                </div>

                {{-- BULAN --}}
                <div id="filter-bulan" class="flex gap-2">
                    <input type="number" name="month" min="1" max="12" placeholder="Bulan"
                           value="{{ request('month') }}"
                           class="text-sm h-9 border-gray-300 rounded-md w-24 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                    <input type="number" name="year" placeholder="Tahun"
                           value="{{ request('year') ?? date('Y') }}"
                           class="text-sm h-9 border-gray-300 rounded-md w-28 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                </div>

                {{-- TAHUN --}}
                <input type="number" name="year" id="filter-tahun"
                       value="{{ request('year') ?? date('Y') }}"
                       class="text-sm h-9 border-gray-300 rounded-md w-28 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">

                <x-primary-button class="h-9">Tampilkan</x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                {{-- JUDUL DINAMIS --}}
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        @php $mode = request('mode', 'tanggal'); @endphp

                        @if($mode == 'tanggal' && request('date'))
                            Aktivitas pada {{ \Carbon\Carbon::parse(request('date'))->format('d F Y') }}
                        @elseif($mode == 'range')
                            Aktivitas dari {{ request('from') }} s/d {{ request('to') }}
                        @elseif($mode == 'bulan')
                            Aktivitas Bulan {{ request('month') }} Tahun {{ request('year') }}
                        @elseif($mode == 'tahun')
                            Aktivitas Tahun {{ request('year') }}
                        @else
                            Aktivitas Hari Ini
                        @endif
                    </h3>

                    {{-- EXPORT --}}
                    @if(in_array(auth()->user()->role, ['manager','admin']))
                        <a href="{{ route('reports.admin-tasks.export', request()->all()) }}"
                           class="px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded hover:bg-green-700">
                            Export Excel
                        </a>
                    @endif
                </div>

                {{-- TABEL --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="text-center py-3 px-4 text-sm font-bold text-gray-700 dark:text-gray-200">Tugas Admin</th>
                                <th class="text-center py-3 px-4 text-sm font-bold text-gray-700 dark:text-gray-200">Proyek</th>
                                <th class="text-center py-3 px-4 text-sm font-bold text-gray-700 dark:text-gray-200">Admin</th>
                                <th class="text-center py-3 px-4 text-sm font-bold text-gray-700 dark:text-gray-200">Status</th>
                                <th class="text-center py-3 px-4 text-sm font-bold text-gray-700 dark:text-gray-200">Waktu Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($reportData as $task)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300">
                                    <td class="py-4 px-4">{{ $task->name }}</td>
                                    <td class="py-4 px-4 text-center">{{ $task->project->name ?? 'Non Proyek' }}</td>
                                    <td class="py-4 px-4 text-center">{{ $task->assignedToAdmin->name ?? '-' }}</td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-200 dark:bg-gray-600 dark:text-gray-100">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        {{ $task->updated_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                        Tidak ada aktivitas admin pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT SWITCH FILTER --}}
    <script>
        function updateFilter() {
            let mode = document.getElementById('mode').value;

            document.getElementById('filter-tanggal').style.display = 'none';
            document.getElementById('filter-range').style.display = 'none';
            document.getElementById('filter-bulan').style.display = 'none';
            document.getElementById('filter-tahun').style.display = 'none';

            if (mode === 'tanggal') document.getElementById('filter-tanggal').style.display = 'block';
            if (mode === 'range') document.getElementById('filter-range').style.display = 'flex';
            if (mode === 'bulan') document.getElementById('filter-bulan').style.display = 'flex';
            if (mode === 'tahun') document.getElementById('filter-tahun').style.display = 'block';
        }

        document.getElementById('mode').addEventListener('change', updateFilter);
        window.onload = updateFilter;
    </script>
</x-app-layout>