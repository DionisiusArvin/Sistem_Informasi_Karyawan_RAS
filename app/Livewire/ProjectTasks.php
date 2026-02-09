<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Str;

class ProjectTasks extends Component
{
    public $projectId;
    public $project;
    public $search = '';

    protected $listeners = [
        'refreshTasks' => '$refresh',
    ];

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->project = Project::with(['tasks.divisions', 'tasks.dailyTasks'])
            ->findOrFail($projectId);
    }

    public function render()
    {
        $project = Project::findOrFail($this->projectId);
        $category = $project->category ?? null;

        $tasks = $project->tasks()
            ->with(['divisions', 'dailyTasks'])
            ->orderBy('order')
            ->get();

        $searchTerm = Str::lower(trim((string) $this->search));

        $pbgOrder = [
            'Data Umum',
            'Data Teknis Arsitektur',
            'Data Teknis Struktur',
            'Data Teknis MEP',
            'Data Tambahan',
            'Upload',
        ];

        $slfOrder = [
            'Data Umum',
            'Data Teknis Arsitektur',
            'Data Teknis Struktur',
            'Data Teknis MEP',
            'Upload',
        ];

        $pavingOrder = [
            'Survey',
            'Gambar Kerja',
            'Engineering Estimate',
            'BOQ',
            'Rencana Kerja dan Syarat2 Teknis',
            'Dokumen Teknis',
            'Harga Perkiraan Sendiri',
            'Laporan',
            'Finalisasi Dokumen Perencanaan',
        ];

        if ($searchTerm !== '') {
            $tasks = $tasks->filter(function ($task) use ($category, $searchTerm, $pavingOrder) {
                $label = $this->getTaskSearchLabel($task, $category, $pavingOrder);
                return Str::contains(Str::lower($label), $searchTerm);
            })->values();
        }

        $tasks = $tasks->sortBy(function ($task) use ($category, $pbgOrder, $slfOrder, $pavingOrder) {
            if ($category === 'PBG') {
                $idx = array_search($task->jenis_tugas, $pbgOrder, true);
                $titleKey = $this->normalizeTitleKey((string) ($task->name ?? ''));
                if ($idx !== false) {
                    return sprintf('0-%s-%03d', $titleKey, $idx);
                }
                return sprintf('0-%s-999', $titleKey);
            }

            if ($category === 'SLF') {
                $idx = array_search($task->jenis_tugas, $slfOrder, true);
                $titleKey = $this->normalizeTitleKey((string) ($task->name ?? ''));
                if ($idx !== false) {
                    return sprintf('0-%s-%03d', $titleKey, $idx);
                }
                return sprintf('0-%s-999', $titleKey);
            }

            if ($category === 'PERENCANAAN' && ($task->jenis_tugas ?? null) === 'Paving') {
                $taskName = (string) ($task->name ?? '');
                [$baseName, $title] = $this->splitPavingName($taskName, $pavingOrder);
                $idx = array_search($baseName, $pavingOrder, true);
                $titleKey = $this->normalizeTitleKey($title !== '' ? $title : $taskName);
                if ($idx !== false) {
                    return sprintf('0-%s-%03d', $titleKey, $idx);
                }
                return sprintf('0-%s-999', $titleKey);
            }

            $judul = trim((string) ($task->name ?? ''));
            if ($judul === '') {
                $judul = (string) ($task->jenis_tugas ?? '');
            }

            return '1-' . Str::lower($judul);
        })->values();

        $project->setRelation('tasks', $tasks);
        $this->project = $project;

        return view('livewire.project-tasks', [
            'project' => $this->project
        ]);
    }

    private function splitPavingName(string $taskName, array $pavingOrder): array
    {
        $name = trim($taskName);
        if ($name === '') {
            return ['', ''];
        }

        $bases = collect($pavingOrder)
            ->filter()
            ->sortByDesc(fn ($item) => strlen($item))
            ->values();

        foreach ($bases as $base) {
            if ($base === '') {
                continue;
            }
            if (Str::startsWith(Str::lower($name), Str::lower($base))) {
                $title = trim(substr($name, strlen($base)));
                $title = ltrim($title, " \t-:\n\r\0\x0B");
                return [$base, $title];
            }
        }

        if (strpos($name, ' - ') !== false) {
            [$baseName, $title] = explode(' - ', $name, 2);
            return [trim($baseName), trim($title)];
        }

        return [$name, ''];
    }

    private function getTaskSearchLabel($task, ?string $category, array $pavingOrder): string
    {
        $name = trim((string) ($task->name ?? ''));
        $jenis = trim((string) ($task->jenis_tugas ?? ''));

        if ($category === 'PERENCANAAN' && $jenis === 'Paving') {
            return $name !== '' ? str_replace(' - ', ' ', $name) : $jenis;
        }

        if ($name !== '') {
            return trim($jenis . ' ' . $name);
        }

        return $jenis;
    }

    private function normalizeTitleKey(string $title): string
    {
        $lower = Str::lower(trim($title));
        if ($lower === '') {
            return 'zzz';
        }

        return preg_replace_callback('/\d+/', function ($m) {
            return str_pad($m[0], 5, '0', STR_PAD_LEFT);
        }, $lower);
    }
}
