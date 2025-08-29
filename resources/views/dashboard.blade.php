<x-app-layout>
    <x-slot name="header">
        <span>
            {{ __('Dashboard') }}
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- KONTEN DINAMIS BERDASARKAN PERAN --}}
            
            {{-- TAMPILAN MANAGER --}}
            @if(auth()->user()->role === 'manager')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-gray-500">Total Proyek Aktif</h3>
                        <p class="text-3xl font-bold mt-2">{{ $totalProjects }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-gray-500">Tugas Menunggu Validasi</h3>
                        <p class="text-3xl font-bold mt-2">{{ $tasksToValidate }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                    <h3 class="font-semibold mb-4">Progress Seluruh Proyek</h3>
                    <canvas id="managerProjectsChart"></canvas>
                </div>
            @endif

            {{-- TAMPILAN KEPALA DIVISI --}}
            @if(auth()->user()->role === 'kepala_divisi')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md"><h3 class="text-gray-500">Tugas Utama</h3><p class="text-3xl font-bold mt-2">{{ $totalTasks }}</p></div>
                    <div class="bg-white p-6 rounded-lg shadow-md"><h3 class="text-gray-500">Menunggu Validasi</h3><p class="text-3xl font-bold mt-2">{{ $tasksToValidate }}</p></div>
                    <div class="bg-white p-6 rounded-lg shadow-md"><h3 class="text-gray-500">Tugas Selesai</h3><p class="text-3xl font-bold mt-2">{{ $statusCounts->get('Selesai', 0) }}</p></div>
                    <div class="bg-white p-6 rounded-lg shadow-md"><h3 class="text-gray-500">Butuh Revisi</h3><p class="text-3xl font-bold mt-2">{{ $statusCounts->get('Revisi', 0) }}</p></div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                    <h3 class="font-semibold mb-4">Distribusi Status Tugas Harian Divisi Anda</h3>
                    <canvas id="kadivTasksChart" class="mx-auto" style="max-width: 300px;"></canvas>
                </div>
            @endif

            {{-- TAMPILAN STAFF --}}
            @if(auth()->user()->role === 'staff')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md"><h3 class="text-gray-500">Tugas Dikerjakan</h3><p class="text-3xl font-bold mt-2">{{ $tasksInProgress }}</p></div>
                    <div class="bg-white p-6 rounded-lg shadow-md"><h3 class="text-gray-500">Menunggu Validasi</h3><p class="text-3xl font-bold mt-2">{{ $tasksToValidate }}</p></div>
                    <div class="bg-white p-6 rounded-lg shadow-md"><h3 class="text-gray-500">Tugas Selesai</h3><p class="text-3xl font-bold mt-2">{{ $tasksCompleted }}</p></div>
                </div>
                 <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                    <h3 class="font-semibold mb-4">Distribusi Status Tugas Saya</h3>
                    <canvas id="staffTasksChart" class="mx-auto" style="max-width: 300px;"></canvas>
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart untuk Manager
            const managerChartEl = document.getElementById('managerProjectsChart');
            if (managerChartEl) {
                new Chart(managerChartEl, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels ?? []),
                        datasets: [{
                            label: 'Progress Pengerjaan (%)',
                            data: @json($chartData ?? []),
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: { scales: { y: { beginAtZero: true, max: 100 } } }
                });
            }

            // Chart untuk Kepala Divisi & Staff
            const kadivChartEl = document.getElementById('kadivTasksChart');
            const staffChartEl = document.getElementById('staffTasksChart');
            const statusData = @json($statusCounts ?? []);
            
            const chartConfig = {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        label: 'Jumlah Tugas',
                        data: Object.values(statusData),
                        backgroundColor: [
                            'rgba(255, 159, 64, 0.7)', // Oranye - Menunggu Validasi
                            'rgba(75, 192, 192, 0.7)', // Hijau - Selesai
                            'rgba(255, 99, 132, 0.7)',  // Merah - Revisi
                            'rgba(153, 102, 255, 0.7)',// Ungu - Dikerjakan
                            'rgba(201, 203, 207, 0.7)'  // Abu-abu - Lainnya
                        ]
                    }]
                }
            };

            if (kadivChartEl) new Chart(kadivChartEl, chartConfig);
            if (staffChartEl) new Chart(staffChartEl, chartConfig);
        });
    </script>
    @endpush
</x-app-layout>