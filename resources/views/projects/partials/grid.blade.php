<div class="py-10">
    <div class="max-w-7xl mx-auto sm:px-4 lg:px-8">
        {{-- Layout Grid untuk Kartu Proyek --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($projects as $project)
                @php
                    $today = \Carbon\Carbon::now();
                    $start = \Carbon\Carbon::parse($project->start_date);
                    $end = \Carbon\Carbon::parse($project->end_date);
                    $daysLeft = floor($today->diffInDays($end, false)); // tanpa koma
                    $totalDays = max($start->diffInDays($end), 1);
                    $daysPassed = max(0, $totalDays - max($daysLeft, 0));
                    $timeProgress = round(($daysPassed / $totalDays) * 100);
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="p-6 flex flex-col justify-between h-full">
                        {{-- Header --}}
                        <div class="flex justify-between items-start">
                            {{-- Kiri: Nama proyek, kode proyek, klien --}}
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $project->name }}
                                </h3>

                                <div class="flex justify-between items-center mt-1">
                                    {{-- Kode proyek di kiri --}}
                                    @if($project->kode_proyek)
                                        <p class="text-xs font-mono text-gray-500 bg-gray-100 dark:bg-gray-700 inline-block px-2 py-0.5 rounded">
                                            {{ $project->kode_proyek }}
                                        </p>
                                    @endif

                                    {{-- Kategori di kanan --}}
                                    @if($project->category)
                                        <p class="text-xs font-semibold text-white bg-blue-600 inline-block px-2 py-0.5 rounded ml-auto">
                                            {{ $project->category }}
                                        </p>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $project->client_name }}
                                </p>

                                {{-- Nilai Kontrak --}}
                                @can('manage-projects')
                                    <p class="text-xs mt-1 text-green-700 dark:text-green-400">
                                        Nilai Kontrak: Rp{{ number_format($project->contract_value, 0, ',', '.') }}
                                    </p>
                                @endcan
                            </div>

                            {{-- Status Kesehatan --}}
                            @php
                                $health = $project->getHealthStatus();
                                $colorClass = match ($health) {
                                    'aman' => 'bg-green-100 text-green-800',
                                    'perhatian' => 'bg-yellow-100 text-yellow-800',
                                    'bahaya' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full {{ $colorClass }}">
                                {{ ucfirst($health) }}
                            </span>
                        </div>
                        {{-- Progress Bar --}}
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Progress</p>
                            <div class="flex items-center mt-1">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-500"
                                         style="width: {{ $project->getProgressPercentage() }}%">
                                    </div>
                                </div>
                                <span class="ml-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $project->getProgressPercentage() }}%
                                </span>
                            </div>
                        </div>

                        {{-- Periode dan Sisa Waktu --}}
                        <div class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Periode:</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}
                            </p>

                            <div class="flex items-center mt-2 space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4 
                                        {{ $project->getProgressPercentage() == 100 
                                            ? 'text-green-500' 
                                            : ($daysLeft > 10 
                                                ? 'text-green-500' 
                                                : ($daysLeft > 0 
                                                    ? 'text-yellow-500' 
                                                    : 'text-red-500')) 
                                        }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10m-11 8h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs font-medium
                                    {{ $daysLeft > 10 ? 'text-green-600' : ($daysLeft > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    @if ($daysLeft > 0)
                                        {{ $daysLeft }} hari tersisa
                                    @elseif ($daysLeft == 0)
                                        Hari terakhir proyek
                                    @elseif($project->getHealthStatus('aman') && $project->getProgressPercentage() == 100)
                                        <span class="bg-green-100 text-green-800 px-1 py-0.5 rounded">Selesai</span>
                                    @else
                                        Terlambat {{ abs($daysLeft) }} hari lalu
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- PIC & Manager --}}
                        <div class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Penanggung Jawab:</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $project->pic ? $project->pic->name : '-' }}
                            </p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end items-center space-x-2 mt-4 border-t border-gray-100 dark:border-gray-700 pt-3">
                            <a href="{{ route('projects.show', $project->id) }}" class="text-gray-400 hover:text-blue-500" title="Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                            -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            @can('manage-projects')
                                <a href="{{ route('projects.edit', $project->id) }}" class="text-gray-400 hover:text-yellow-500" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036
                                                a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus proyek ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                    a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1
                                                    -10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 text-center text-gray-500">
                    Belum ada proyek yang dibuat.
                </div>
            @endforelse
        </div>
    </div>
</div>
