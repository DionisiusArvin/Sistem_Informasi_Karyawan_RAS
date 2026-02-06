<div class="space-y-4">
    @forelse ($projects as $project)
        @php
            $today = \Carbon\Carbon::now();
            $start = \Carbon\Carbon::parse($project->start_date);
            $end = \Carbon\Carbon::parse($project->end_date);
            $daysLeft = floor($today->diffInDays($end, false));
            $totalDays = max($start->diffInDays($end), 1);
            $daysPassed = max(0, $totalDays - max($daysLeft, 0));
            $timeProgress = round(($daysPassed / $totalDays) * 100);
        @endphp

        <div 
            x-data="{ openProject: false }" 
            class="flex flex-col bg-white dark:bg-gray-800 px-5 py-4 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200"
        >
            {{-- HEADER PROYEK --}}
            <div class="flex justify-between items-start">
                {{-- Kolom kiri --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $project->name }}
                        </h3>
                        {{-- Tombol expand proyek --}}
                        <button @click="openProject = !openProject" class="text-gray-500 hover:text-blue-500 transition">
                            <svg x-show="!openProject" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <svg x-show="openProject" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Kode dan kategori --}}
                    <div class="flex items-center gap-2 mt-1">
                        @if($project->kode_proyek)
                            <span class="text-xs font-mono text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                {{ $project->kode_proyek }}
                            </span>
                        @endif
                        @if($project->category)
                            <span class="text-xs font-semibold text-white bg-blue-600 px-2 py-0.5 rounded">
                                {{ $project->category }}
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 truncate">
                        {{ $project->client_name }}
                    </p>

                    @if($project->pic)
                        <p class="text-xs text-gray-500 mt-1">
                            PIC: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $project->pic->name }}</span>
                        </p>
                    @endif

                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}
                        –
                        {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                    </p>

                    {{-- Nilai kontrak hanya untuk manager --}}
                    @can('manage-projects')
                        @if(!is_null($project->contract_value))
                            <p class="text-xs mt-1 text-green-700 dark:text-green-400">
                                Nilai Kontrak: Rp{{ number_format($project->contract_value, 0, ',', '.') }}
                            </p>
                        @endif
                    @endcan
                </div>

                {{-- Kolom kanan --}}
                <div class="flex flex-col items-end space-y-2 ml-4">
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

                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 min-w-[40px] text-right">
                            {{ $project->getProgressPercentage() }}%
                        </span>

                        {{-- Detail --}}
                        <a href="{{ route('projects.show', $project->id) }}"
                           class="text-gray-400 hover:text-blue-500 transition"
                           title="Detail proyek">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5
                                         c4.478 0 8.268 2.943 9.542 7
                                         -1.274 4.057-5.064 7-9.542 7
                                         -4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        @can('manage-projects')
                            {{-- Edit --}}
                            <a href="{{ route('projects.edit', $project->id) }}"
                               class="text-gray-400 hover:text-yellow-500 transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036
                                            a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>

                            {{-- Hapus --}}
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus proyek ini?');"
                                  class="inline-block flex items-center">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Hapus">
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

                    {{-- Status waktu --}}
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
            </div>

            {{-- BAGIAN EXPAND: Daftar tugas utama --}}
            <div x-show="openProject" x-collapse class="mt-4 pl-6 border-l border-gray-200 dark:border-gray-700 space-y-2">
                @forelse ($project->tasks as $task)
                    <div x-data="{ openTask: false }" class="bg-gray-50 dark:bg-gray-900 px-3 py-2 rounded-md">
                        <div class="flex justify-between items-center">
                            <button @click="openTask = !openTask" class="flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:text-blue-500">
                                <svg x-show="!openTask" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <svg x-show="openTask" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span>
                                    @if(($task->jenis_tugas ?? null) === 'Paving')
                                        {{ $task->name ? str_replace(' - ', ' ', $task->name) : '' }}
                                    @else
                                        {{ $task->name }}
                                    @endif
                                </span>
                            </button>
                            <span class="text-xs text-gray-500">{{ $task->getProgressPercentage() ?? 0 }}%</span>
                        </div>

                        {{-- Tugas harian --}}
                        <div x-show="openTask" x-collapse class="mt-2 pl-5 space-y-1">
                            @forelse ($task->dailyTasks as $daily)
                                <div class="flex justify-between items-center text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded">
                                    <span>{{ $daily->name }}</span>
                                    <span class="
                                        px-2 py-0.5 rounded-full
                                        {{ $daily->status == 'selesai' ? 'bg-green-100 text-green-700' :
                                           ($daily->status == 'proses' ? 'bg-yellow-100 text-yellow-700' :
                                           'bg-gray-100 text-gray-700') }}">
                                        {{ ucfirst($daily->status) }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 italic pl-2">Tidak ada tugas harian</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 italic pl-2">Belum ada tugas utama</p>
                @endforelse
            </div>
        </div>
    @empty
        <div class="text-center text-gray-500 dark:text-gray-400 py-6">
            Belum ada proyek yang dibuat.
        </div>
    @endforelse
</div>
