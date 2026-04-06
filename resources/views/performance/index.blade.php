<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
            Performa Karyawan
        </h2>
    </x-slot>

    {{-- ================= SWITCH MODE (FIX) ================= --}}
    <div class="mb-4 flex gap-2">
        <a href="/performance?type=staf"
           class="px-4 py-2 rounded 
           {{ ($type ?? 'staf') == 'staf' ? 'bg-blue-600 text-white' : 'bg-gray-500 text-white' }}">
            Performa Staf
        </a>

        <a href="/performance?type=kepala"
           class="px-4 py-2 rounded 
           {{ ($type ?? '') == 'kepala' ? 'bg-green-600 text-white' : 'bg-gray-500 text-white' }}">
            Kepala Divisi
        </a>
    </div>

    {{-- ================= FORM FILTER (FIX TYPE 🔥) ================= --}}
    <form method="GET" action="{{ route('performance.calculate') }}"
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6
                 border border-gray-200 dark:border-gray-700">

        {{-- 🔥 PENTING BANGET --}}
        <input type="hidden" name="type" value="{{ $type ?? 'staf' }}">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- KARYAWAN --}}
            <div>
                <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">
                    Karyawan
                </label>
                <select name="user_id"
                        class="w-full rounded border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900
                               text-gray-900 dark:text-gray-100">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}"
                            @selected(($userId ?? null) == $emp->id)>
                            {{ $emp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- PERIODE --}}
            <div>
                <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">
                    Periode
                </label>
                <select name="period" required
                        class="w-full rounded border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900
                               text-gray-900 dark:text-gray-100">
                    <option value="1"  @selected(($period ?? 1) == 1)>1 Bulan</option>
                    <option value="6"  @selected(($period ?? 1) == 6)>6 Bulan</option>
                    <option value="12" @selected(($period ?? 1) == 12)>12 Bulan</option>
                </select>
            </div>

            {{-- STATUS --}}     
            <div>
                <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">
                    Status Tugas
                </label>
                <select name="status"
                        class="w-full rounded border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900
                               text-gray-900 dark:text-gray-100">
                    <option value="semua" @selected(($status ?? 'semua') == 'semua')>
                        Semua
                    </option>
                    <option value="selesai" @selected(($status ?? 'semua') == 'selesai')>
                        Selesai
                    </option>
                    <option value="proses" @selected(($status ?? 'semua') == 'proses')>
                        Proses
                    </option>
                </select>
            </div> 
        </div>

        <button type="submit"
                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Hitung KPI
        </button>
    </form>

    {{-- ================= HASIL KPI ================= --}}
    @isset($results)

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8
                    border border-gray-200 dark:border-gray-700">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Hasil Performa ({{ $period }} Bulan)
                </h3>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('performance.pdf', ['type' => $type ?? 'staf', 'period' => $period, 'status' => $status ?? 'semua', 'user_id' => $userId ?? null]) }}"
                       target="_blank"
                       class="inline-flex items-center rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        Cetak PDF
                    </a>

                    <a href="{{ route('performance.excel', ['type' => $type ?? 'staf', 'period' => $period, 'status' => $status ?? 'semua', 'user_id' => $userId ?? null]) }}"
                       class="inline-flex items-center rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Export Excel
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full border-collapse text-left text-sm text-gray-900 dark:text-gray-100">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Ranking</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Nama</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap text-center">Total Tugas</th>
                            @if(($type ?? 'staf') === 'kepala')
                                <th class="px-4 py-3 font-semibold whitespace-nowrap text-center">Kap. Produksi (30%)</th>
                                <th class="px-4 py-3 font-semibold whitespace-nowrap text-center">Nilai Kepala (70%)</th>
                            @endif
                            <th class="px-4 py-3 font-semibold whitespace-nowrap text-center">Skor KPI</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap text-center">Badge</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($results as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 font-bold whitespace-nowrap" style="color: {{ $row->rank_color }}">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-base">{{ $row->rank_icon }}</span>
                                    <span>#{{ $row->rank }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium whitespace-nowrap">{{ str_replace('_', ' ', $row->name) }}</td>
                            <td class="px-4 py-3 text-center">{{ $row->total_tasks }}</td>
                            @if(($type ?? 'staf') === 'kepala')
                                <td class="px-4 py-3 text-center">{{ $row->kapasitas_produksi ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $row->nilai_kepala ?? '-' }}</td>
                            @endif
                            <td class="px-4 py-3 font-bold text-center text-base">
                                {{ $row->final_score }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold text-white shadow-sm"
                                      style="background: {{ $row->rank_color }}">
                                    {{ $row->badge }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= GRAFIK ================= --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow
                    border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                Grafik KPI Karyawan
            </h3>
            <canvas id="kpiChart"></canvas>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {

            const ctx = document.getElementById('kpiChart');
            let chart;

            function renderChart() {
                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($results->pluck('name')) !!},
                        datasets: [{
                            label: 'Skor KPI',
                            data: {!! json_encode($results->pluck('final_score')) !!},
                        }]
                    }
                });
            }

            renderChart();
        });
        </script>
        @endpush

    @endisset

</x-app-layout>
