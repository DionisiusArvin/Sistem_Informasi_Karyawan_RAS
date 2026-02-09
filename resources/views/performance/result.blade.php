<x-app-layout>

    <h2 class="text-xl font-bold mb-4">Hasil Performa</h2>

    <p class="mb-4 text-gray-600 dark:text-gray-300">
        Periode:
        <span class="font-semibold">
            {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
        </span>
    </p>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 text-left">
                    <th class="p-3">Nama</th>
                    <th class="p-3">Total Tugas</th>
                    <th class="p-3">Skor KPI</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $row)
                    @php
                        $nilai = $row->final_score;
                        $status = str_contains($row->badge, 'Top Performer')
                            ? 'Top Performer'
                            : 'Needs Improvement';
                        $warna = str_contains($row->badge, 'Top Performer') ? 'green' : 'red';
                    @endphp
                    <tr class="border-t">
                        <td class="p-3">{{ $row->name }}</td>
                        <td class="p-3">{{ $row->total_tasks }}</td>
                        <td class="p-3 font-bold">{{ $nilai }}</td>
                        <td class="p-3">
                            <span class="px-3 py-1 rounded-full text-white bg-{{ $warna }}-600">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-app-layout>
