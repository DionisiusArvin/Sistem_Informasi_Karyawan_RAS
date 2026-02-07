<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
            Performa Karyawan
        </h2>
    </x-slot>

    {{-- ================= FORM FILTER ================= --}}
    <form method="POST" action="{{ route('performance.calculate') }}"
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6
                 border border-gray-200 dark:border-gray-700">
        @csrf

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
                    <option value="valid" @selected(($status ?? 'semua') == 'valid')>
                        Valid
                    </option>
                </select>
            </div>

        </div>

        <button type="submit"
                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Hitung KPI
        </button>
    </form>

    @isset($results)

        {{-- ================= TABEL ================= --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8
                    border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                Hasil Performa ({{ $period }} Bulan)
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Skor harian = bobot x progres (%) / 8. Skor periode adalah penjumlahan skor harian.
            </p>

            <table class="w-full border-collapse text-gray-900 dark:text-gray-100">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2">Ranking</th>
                        <th class="text-left py-2">Nama</th>
                        <th class="text-left py-2">Total Tugas</th>
                        <th class="text-left py-2">Skor KPI</th>
                        <th class="text-left py-2">Badge</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($results as $row)
                    <tr class="border-b border-gray-200 dark:border-gray-700">

                        <td class="py-2 font-bold" style="color: {{ $row->rank_color }}">
                            {{ $row->rank_icon }} #{{ $row->rank }}
                        </td>

                        <td class="py-2">{{ $row->name }}</td>

                        <td class="py-2">{{ $row->total_tasks }}</td>

                        <td class="py-2 font-bold">
                            {{ $row->final_score }}
                        </td>

                        <td class="py-2">
                            <span class="px-3 py-1 rounded-full text-white"
                                  style="background: {{ $row->rank_color }}">
                                {{ $row->badge }}
                            </span>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- ================= GRAFIK ================= --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow
                    border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                Grafik KPI Karyawan
            </h3>
            <canvas id="kpiChart"></canvas>
        </div>

        {{-- ================= CHART.JS AUTO DARK MODE ================= --}}
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {

            const ctx = document.getElementById('kpiChart');
            let chart;

            function renderChart() {

                const isDark = document.documentElement.classList.contains('dark');
                const fontColor = isDark ? '#e5e7eb' : '#1f2937';
                const gridColor = isDark ? '#374151' : '#e5e7eb';

                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($results->pluck('name')) !!},
                        datasets: [{
                            label: 'Skor KPI',
                            data: {!! json_encode($results->pluck('final_score')) !!},
                            backgroundColor: '#3b82f6'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                ticks: { color: fontColor },
                                grid: { color: gridColor }
                            },
                            y: {
                                ticks: { color: fontColor },
                                grid: { color: gridColor },
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                labels: { color: fontColor }
                            }
                        }
                    }
                });
            }

            renderChart();

            const observer = new MutationObserver(renderChart);
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });

        });
        </script>
        @endpush

    @endisset
</x-app-layout>
    
