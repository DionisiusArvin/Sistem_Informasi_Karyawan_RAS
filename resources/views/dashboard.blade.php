<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ===================== MANAGER ===================== --}}
            @if(auth()->user()->role === 'manager')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

                    {{-- Total Proyek --}}
                    <div class="p-6 rounded-2xl shadow-md bg-gradient-to-br from-blue-100 via-blue-50 to-white 
                                dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border border-blue-200/50
                                dark:border-gray-700 transition hover:scale-[1.02] hover:shadow-xl">
                        <div>
                            <div class="text-lg font-medium text-gray-800 dark:text-gray-200">
                                Total Proyek
                            </div>
                            <div class="mt-3 text-4xl font-extrabold text-gray-900 dark:text-white">
                                {{ $totalProjects ?? 0 }}
                            </div>
                        </div>
                    </div>

                    {{-- Total Omset --}}
                    <div class="p-6 rounded-2xl shadow-md bg-gradient-to-br from-green-100 via-green-50 to-white 
                                dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border border-green-200/50
                                dark:border-gray-700 transition hover:scale-[1.02] hover:shadow-xl">
                        <div>
                            <div class="text-lg font-medium text-gray-800 dark:text-gray-200">
                                Total Omset Tahun Ini
                            </div>
                            <div class="mt-3 text-4xl font-extrabold text-gray-900 dark:text-white">
                                Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    {{-- Validasi --}}
                    <div class="p-6 rounded-2xl shadow-md bg-gradient-to-br from-yellow-100 via-yellow-50 to-white
                                dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border border-yellow-200/50
                                dark:border-gray-700 transition hover:scale-[1.02] hover:shadow-xl">
                        <div>
                            <div class="text-lg font-medium text-gray-800 dark:text-gray-200">
                                Tugas Menunggu Validasi
                            </div>
                            <div class="mt-3 text-4xl font-extrabold text-gray-900 dark:text-white">
                                {{ $tasksToValidate ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chart --}}
                <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold mb-6 text-xl text-gray-900 dark:text-gray-200">
                        Progress Seluruh Proyek
                    </h3>
                    <canvas id="managerProjectsChart"></canvas>
                </div>
            @endif

            {{-- ===================== KEPALA DIVISI ===================== --}}
            @if(auth()->user()->role === 'kepala_divisi')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 transition hover:shadow-xl hover:scale-[1.01]">
                        <h3 class="text-gray-700 dark:text-gray-300 text-lg font-medium">Tugas Utama</h3>
                        <p class="text-4xl font-bold mt-3 text-gray-900 dark:text-white text-center">{{ $totalTasks }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 transition hover:shadow-xl hover:scale-[1.01]">
                        <h3 class="text-gray-700 dark:text-gray-300 text-lg font-medium">Menunggu Validasi</h3>
                        <p class="text-4xl font-bold mt-3 text-gray-900 dark:text-white text-center">{{ $tasksToValidate }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 transition hover:shadow-xl hover:scale-[1.01]">
                        <h3 class="text-gray-700 dark:text-gray-300 text-lg font-medium">Tugas Selesai</h3>
                        <p class="text-4xl font-bold mt-3 text-gray-900 dark:text-white text-center">{{ $statusCounts->get('Selesai', 0) }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 transition hover:shadow-xl hover:scale-[1.01]">
                        <h3 class="text-gray-700 dark:text-gray-300 text-lg font-medium">Butuh Revisi</h3>
                        <p class="text-4xl font-bold mt-3 text-gray-900 dark:text-white text-center">{{ $statusCounts->get('Revisi', 0) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10">

                    {{-- Chart --}}
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-gray-200 text-lg">
                            Distribusi Status Tugas Harian
                        </h3>
                        <canvas id="kadivTasksChart" class="mx-auto" style="max-width: 300px;"></canvas>
                    </div>

                    {{-- Jadwal --}}
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Jadwal Divisi
                            </h3>
                            <a href="{{ route('schedules.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Lihat Semua →
                            </a>
                        </div>

                        @forelse ($jadwalDivisi as $row)
                            <div class="mb-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 
                                        bg-gray-50 dark:bg-gray-900 shadow-sm hover:shadow-md transition">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $row->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($row->date)->translatedFormat('d F Y') }}
                                        </p>
                                    </div>

                                    <span class="text-xs px-3 py-1 rounded-full font-semibold
                                        @if($row->status === 'pending') bg-yellow-200 text-yellow-800
                                        @elseif($row->status === 'selesai') bg-green-200 text-green-800
                                        @else bg-blue-200 text-blue-800 @endif">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada jadwal ditemukan.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- ===================== STAFF ===================== --}}
            @if(auth()->user()->role === 'staff')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 transition hover:shadow-xl hover:scale-[1.01]">
                        <h3 class="text-gray-700 dark:text-gray-300 text-lg font-medium">Tugas Dikerjakan</h3>
                        <p class="mt-3 text-4xl font-bold text-gray-900 dark:text-white">{{ $tasksInProgress }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 transition hover:shadow-xl hover:scale-[1.01]">
                        <h3 class="text-gray-700 dark:text-gray-300 text-lg font-medium">Menunggu Validasi</h3>
                        <p class="mt-3 text-4xl font-bold text-gray-900 dark:text-white">{{ $tasksToValidate }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 transition hover:shadow-xl hover:scale-[1.01]">
                        <h3 class="text-gray-700 dark:text-gray-300 text-lg font-medium">Tugas Selesai</h3>
                        <p class="mt-3 text-4xl font-bold text-gray-900 dark:text-white">{{ $tasksCompleted }}</p>
                    </div>

                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 mt-10">
                    <h3 class="font-semibold mb-6 text-gray-900 dark:text-gray-200 text-lg">
                        Distribusi Status Tugas Saya
                    </h3>
                    <canvas id="staffTasksChart" class="mx-auto" style="max-width: 300px;"></canvas>
                </div>
            @endif

        </div>
    </div>

    {{-- Script tetap sama --}}
    @push('scripts')
        <script>
    document.addEventListener('DOMContentLoaded', function () {

        // Fungsi untuk mengambil warna berdasarkan mode
        function getColors() {
            const isDark = document.documentElement.classList.contains('dark');

            return {
                text: isDark ? '#e5e7eb' : '#1f2937',        // gray-200 / gray-800
                grid: isDark ? '#374151' : '#e5e7eb',        // gray-700 / gray-200
                border: isDark ? '#4b5563' : '#9ca3af',      // gray-600 / gray-400
                bgProgress: isDark ? 'rgba(59,130,246,0.6)' : 'rgba(59,130,246,0.4)', // biru
            };
        }

        function applyDarkMode(chart) {
            const c = getColors();

            // Update tiap chart
            chart.options.scales.y.ticks.color = c.text;
            chart.options.scales.x.ticks.color = c.text;

            chart.options.scales.y.grid.color = c.grid;
            chart.options.scales.x.grid.color = c.grid;

            chart.data.datasets.forEach(ds => {
                if (ds.label === "Progress Pengerjaan (%)") {
                    ds.backgroundColor = c.bgProgress;
                    ds.borderColor = c.border;
                }
            });

            chart.update();
        }

        // === CHART MANAGER ===
        const managerEl = document.getElementById('managerProjectsChart');
        let managerChart = null;

        if (managerEl) {
            const c = getColors();

            managerChart = new Chart(managerEl, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels ?? []),
                    datasets: [{
                        label: "Progress Pengerjaan (%)",
                        data: @json($chartData ?? []),
                        backgroundColor: c.bgProgress,
                        borderColor: c.border,
                        borderWidth: 1,
                    }]
                },
                options: {
                    plugins: {
                        legend: { labels: { color: c.text } }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            max: 100,
                            ticks: { color: c.text },
                            grid: { color: c.grid }
                        },
                        x: {
                            ticks: { color: c.text },
                            grid: { color: c.grid }
                        }
                    }
                }
            });
        }

        // === DOUGHNUT (Kadiv & Staff) ===
        const doughnutEls = [
            document.getElementById('kadivTasksChart'),
            document.getElementById('staffTasksChart')
        ];
        const statusData = @json($statusCounts ?? []);

        const doughnutColors = [
            'rgba(255, 159, 64, 0.7)',   
            'rgba(75, 192, 192, 0.7)',  
            'rgba(255, 99, 132, 0.7)',  
            'rgba(153, 102, 255, 0.7)',
            'rgba(201, 203, 207, 0.7)'
        ];

        let doughnutCharts = [];

        doughnutEls.forEach(el => {
            if (!el) return;

            const chart = new Chart(el, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: doughnutColors
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                color: getColors().text
                            }
                        }
                    }
                }
            });

            doughnutCharts.push(chart);
        });

        // === APPLY DARK MODE ON CHANGE ===
const observer = new MutationObserver(() => {
    const c = getColors();

    // === MANAGER CHART ===
    if (managerChart) {
        managerChart.options.plugins.legend.labels.color = c.text;
        managerChart.options.scales.x.ticks.color = c.text;
        managerChart.options.scales.y.ticks.color = c.text;
        managerChart.options.scales.x.grid.color = c.grid;
        managerChart.options.scales.y.grid.color = c.grid;

        managerChart.data.datasets.forEach(ds => {
            if (ds.label === "Progress Pengerjaan (%)") {
                ds.backgroundColor = c.bgProgress;
                ds.borderColor = c.border;
            }
        });

        managerChart.update();
    }

    // === DOUGHNUT CHART ===
    doughnutCharts.forEach(chart => {
        chart.options.plugins.legend.labels.color = c.text;
        chart.update();
    });
});

observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
});

        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    });
    </script>
    @endpush

</x-app-layout>
