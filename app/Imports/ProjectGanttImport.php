<?php

namespace App\Imports;

use App\Models\Project;
use App\Models\Task;
use App\Models\DailyTask;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectGanttImport implements WithEvents
{
    use RegistersEventListeners;

    protected Project $project;
    protected array $importedTasks = [];

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->parseSheet($event->getSheet()->getDelegate());
            },
        ];
    }

    private function parseSheet(Worksheet $sheet): void
    {
        $highestRow = $sheet->getHighestRow();
        $currentTask = null;
        $taskOrder = $this->project->tasks()->max('order') ?? 0;

        // Start from row 3 (skip title row 1 + header row 2)
        for ($row = 3; $row <= $highestRow; $row++) {
            $noCell   = trim((string) $sheet->getCell("A{$row}")->getValue());
            $nameCell = trim((string) $sheet->getCell("B{$row}")->getValue());

            // Skip empty rows
            if (empty($nameCell)) {
                continue;
            }

            if (!empty($noCell) && is_numeric($noCell)) {
                // ── This is a Main Task (tugas utama) ──
                $taskOrder++;

                // Check if task with this name already exists in the project
                $existingTask = $this->project->tasks()
                    ->where('name', $nameCell)
                    ->first();

                if ($existingTask) {
                    $currentTask = $existingTask;
                } else {
                    $currentTask = Task::create([
                        'project_id'  => $this->project->id,
                        'name'        => $nameCell,
                        'description' => null,
                        'order'       => $taskOrder,
                    ]);
                }

                $this->importedTasks[] = [
                    'type' => 'task',
                    'name' => $nameCell,
                    'id'   => $currentTask->id,
                    'new'  => !$existingTask,
                ];

            } else {
                // ── This is a Daily Task (tugas harian / sub-item) ──
                if (!$currentTask) {
                    continue; // Skip orphaned daily tasks
                }

                $cleanName = ltrim($nameCell); // Remove indentation

                // Check if daily task with this name already exists under the task
                $existingDaily = $currentTask->dailyTasks()
                    ->where('name', $cleanName)
                    ->first();

                if (!$existingDaily) {
                    DailyTask::create([
                        'task_id'    => $currentTask->id,
                        'project_id' => $this->project->id,
                        'name'       => $cleanName,
                        'status'     => 'Belum Dikerjakan',
                        'weight'     => 1,
                    ]);

                    $this->importedTasks[] = [
                        'type' => 'daily_task',
                        'name' => $cleanName,
                        'new'  => true,
                    ];
                }
            }
        }
    }

    public function getImportedTasks(): array
    {
        return $this->importedTasks;
    }
}
