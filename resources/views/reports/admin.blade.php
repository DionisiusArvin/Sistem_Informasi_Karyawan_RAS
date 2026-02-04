<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4 md:mb-0">
                Laporan Tugas Admin
            </h2>

            <div class="flex items-center space-x-4 w-full md:w-auto">
                <form action="{{ route('reports.admin-tasks') }}" method="GET" class="flex items-center space-x-2 flex-grow">
                    @if(auth()->user()->role === 'manager')
                        <select name="user_id" class="text-sm h-9 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option value="">Semua Admin</option>
                            @foreach($filterableUsers as $filterableUser)
                                <option value="{{ $filterableUser->id }}" @selected($selectedUserId == $filterableUser->id)>
                                    {{ $filterableUser->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <x-text-input id="date" type="date" name="date" :value="request('date', $selectedDate)" class="text-sm h-9 w-full"/>
                    <x-primary-button class="h-9">Tampilkan</x-primary-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Aktivitas pada {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}
                        </h3>
                        @if(in_array(auth()->user()->role, ['manager', 'admin']))
                            <a href="{{ route('reports.admin-tasks.export', ['date' => $selectedDate, 'user_id' => $selectedUserId]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Export Excel
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Tugas Admin</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Proyek</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Admin</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Status</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 dark:text-gray-400 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($reportData as $task)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-4 px-4 font-medium text-gray-900 dark:text-white">{{ $task->name }}</td>
                                        <td class="py-4 px-4 text-center text-sm">{{ $task->project->name ?? 'Non Proyek' }}</td>
                                        <td class="py-4 px-4 text-center text-sm">{{ $task->assignedToAdmin->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-4 text-center">
                                            @php
                                                $statusColor = match ($task->status) {
                                                    'Selesai' => 'bg-green-100 text-green-800',
                                                    'Menunggu Validasi' => 'bg-orange-100 text-orange-800',
                                                    'Revisi' => 'bg-red-100 text-red-800',
                                                    'Belum Dikerjakan' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full {{ $statusColor }}">
                                                {{ $task->status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center text-sm font-semibold">{{ $task->updated_at->format('H:i') }} WIB</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-gray-500">Tidak ada aktivitas admin pada tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
