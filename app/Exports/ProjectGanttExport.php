<?php

namespace App\Exports;

use App\Models\DailyTask;
use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectGanttExport implements WithEvents, WithTitle
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function title(): string
    {
        return substr($this->project->name, 0, 31);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->buildSheet($event->sheet->getDelegate());
            },
        ];
    }

    private function buildSheet(Worksheet $sheet): void
    {
        $project = $this->project->load('tasks.dailyTasks.activities');

        $startDate = Carbon::parse($project->start_date)->startOfDay();
        $printedAt = Carbon::today()->startOfDay();
        $endDate = $printedAt->greaterThanOrEqualTo($startDate)
            ? $printedAt
            : $startDate->copy();

        $dates = collect(CarbonPeriod::create($startDate, '1 day', $endDate));

        $totalDates = $dates->count();
        $lastDateColIndex = 2 + $totalDates;
        $lastDateCol = Coordinate::stringFromColumnIndex($lastDateColIndex);

        $sheet->mergeCells("A1:{$lastDateCol}1");
        $sheet->setCellValue('A1', "Pekerjaan {$project->name}");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->setCellValue('A2', 'No.');
        $sheet->setCellValue('B2', 'Check list Pekerjaan');

        $offDayColumns = [];
        foreach ($dates as $i => $date) {
            $col = Coordinate::stringFromColumnIndex(3 + $i);
            $sheet->setCellValue("{$col}2", $date->format('j/n/Y'));
            $sheet->getColumnDimension($col)->setWidth(11);

            $isOffDay = $this->isWeekendOrConfiguredHoliday($date);
            if ($isOffDay) {
                $offDayColumns[] = $col;
            }

            if ($date->isSameDay($printedAt)) {
                $sheet->getStyle("{$col}2")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C00000']],
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                ]);
            } elseif ($isOffDay) {
                $sheet->getStyle("{$col}2")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCE4D6']],
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '7F2F00']],
                ]);
            } else {
                $sheet->getStyle("{$col}2")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                ]);
            }
        }

        $sheet->getStyle("A2:{$lastDateCol}2")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A2:B2')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
        ]);

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $dailySnapshots = [];
        foreach ($project->tasks as $task) {
            foreach ($task->dailyTasks as $dailyTask) {
                $dailySnapshots[$dailyTask->id] = $this->buildDailySnapshots($dailyTask, $dates, $printedAt);
            }
        }

        $currentRow = 3;
        $taskNo = 0;

        foreach ($project->tasks as $task) {
            $taskNo++;

            $sheet->setCellValue("A{$currentRow}", $taskNo);
            $sheet->setCellValue("B{$currentRow}", $task->name);

            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle("B{$currentRow}")->applyFromArray([
                'font' => ['bold' => true],
            ]);

            foreach ($dates as $i => $date) {
                $col = Coordinate::stringFromColumnIndex(3 + $i);
                $dateKey = $date->toDateString();

                $progressValues = [];
                $hasActivityToDate = false;

                foreach ($task->dailyTasks as $dailyTask) {
                    $snapshot = $dailySnapshots[$dailyTask->id] ?? null;
                    if (!$snapshot) {
                        continue;
                    }

                    $progressForDate = $snapshot['progress_by_date'][$dateKey] ?? null;
                    if ($progressForDate !== null) {
                        $progressValues[] = $progressForDate;
                    }

                    $firstActivityDate = $snapshot['first_activity_date'];
                    if ($firstActivityDate && $date->greaterThanOrEqualTo($firstActivityDate)) {
                        $hasActivityToDate = true;
                    }
                }

                if (count($progressValues) > 0) {
                    $avgProgress = (int) round(array_sum($progressValues) / count($progressValues));
                    $sheet->setCellValue("{$col}{$currentRow}", $avgProgress . '%');

                    if ($avgProgress < 100) {
                        $this->applyRedStyle($sheet, "{$col}{$currentRow}");
                    } else {
                        $this->applyGreenStyle($sheet, "{$col}{$currentRow}");
                    }
                } elseif ($hasActivityToDate) {
                    $this->applyRedBar($sheet, "{$col}{$currentRow}");
                } elseif ($this->isWeekendOrConfiguredHoliday($date)) {
                    $this->applyOffDayCellStyle($sheet, "{$col}{$currentRow}");
                }
            }

            $sheet->getStyle("A{$currentRow}:{$lastDateCol}{$currentRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $currentRow++;

            foreach ($task->dailyTasks as $dailyTask) {
                $sheet->setCellValue("B{$currentRow}", '      ' . $dailyTask->name);
                $sheet->getStyle("B{$currentRow}")->applyFromArray([
                    'font' => ['size' => 10],
                    'alignment' => ['indent' => 3],
                ]);

                $snapshot = $dailySnapshots[$dailyTask->id] ?? null;
                $firstActivityDate = $snapshot['first_activity_date'] ?? null;
                $lastActivityDate = $snapshot['last_activity_date'] ?? null;

                foreach ($dates as $i => $date) {
                    $col = Coordinate::stringFromColumnIndex(3 + $i);
                    $dateKey = $date->toDateString();
                    $progressForDate = $snapshot['progress_by_date'][$dateKey] ?? null;

                    $isInGanttRange = $firstActivityDate && $lastActivityDate
                        && $date->greaterThanOrEqualTo($firstActivityDate)
                        && $date->lessThanOrEqualTo($lastActivityDate);

                    if ($progressForDate !== null) {
                        $sheet->setCellValue("{$col}{$currentRow}", $progressForDate . '%');

                        if ($progressForDate < 100) {
                            $this->applyRedStyle($sheet, "{$col}{$currentRow}");
                        } else {
                            $this->applyGreenStyle($sheet, "{$col}{$currentRow}");
                        }
                    } elseif ($isInGanttRange) {
                        $this->applyRedBar($sheet, "{$col}{$currentRow}");
                    } elseif ($this->isWeekendOrConfiguredHoliday($date)) {
                        $this->applyOffDayCellStyle($sheet, "{$col}{$currentRow}");
                    }
                }

                $sheet->getStyle("A{$currentRow}:{$lastDateCol}{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $currentRow++;
            }
        }

        $sheet->freezePane('C3');
        $sheet->getStyle("A2:{$lastDateCol}" . ($currentRow - 1))->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Pastikan kolom libur yang masih kosong tetap berwarna (tanpa menimpa sel progres).
        foreach ($offDayColumns as $col) {
            for ($row = 3; $row < $currentRow; $row++) {
                $cell = "{$col}{$row}";
                if ((string) $sheet->getCell($cell)->getValue() === '') {
                    $this->applyOffDayCellStyle($sheet, $cell);
                }
            }
        }
    }

    private function buildDailySnapshots(DailyTask $dailyTask, $dates, Carbon $printedAt): array
    {
        $sortedActivities = $dailyTask->activities
            ->sortBy('created_at')
            ->values();

        $firstActivityDate = $sortedActivities->isNotEmpty()
            ? Carbon::parse($sortedActivities->first()->created_at)->startOfDay()
            : null;
        $lastActivityDate = $sortedActivities->isNotEmpty()
            ? Carbon::parse($sortedActivities->last()->created_at)->startOfDay()
            : null;

        $progressEvents = $sortedActivities
            ->map(function ($activity) {
                $resolvedProgress = $this->resolveProgressFromActivity($activity);
                if ($resolvedProgress === null) {
                    return null;
                }

                return [
                    'at' => Carbon::parse($activity->created_at),
                    'progress' => $resolvedProgress,
                ];
            })
            ->filter()
            ->values();

        $progressByDate = [];
        $eventPointer = 0;
        $currentProgress = null;

        foreach ($dates as $date) {
            $dayEnd = $date->copy()->endOfDay();
            while (
                $eventPointer < $progressEvents->count()
                && $progressEvents[$eventPointer]['at']->lessThanOrEqualTo($dayEnd)
            ) {
                $currentProgress = $progressEvents[$eventPointer]['progress'];
                $eventPointer++;
            }

            $progressForDate = $currentProgress;
            if ($date->isSameDay($printedAt)) {
                $progressForDate = (int) $dailyTask->progress;
            }

            $progressByDate[$date->toDateString()] = $progressForDate;
        }

        return [
            'progress_by_date' => $progressByDate,
            'first_activity_date' => $firstActivityDate,
            'last_activity_date' => $lastActivityDate,
        ];
    }

    private function resolveProgressFromActivity($activity): ?int
    {
        if ($activity->progress_percent !== null) {
            $value = (int) round((float) $activity->progress_percent);
            return max(0, min(100, $value));
        }

        if ($activity->activity_type === 'permintaan_revisi') {
            return 50;
        }

        return null;
    }

    private function applyGreenStyle(Worksheet $sheet, string $cell): void
    {
        $sheet->getStyle($cell)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '92D050']],
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    private function applyRedBar(Worksheet $sheet, string $cell): void
    {
        $sheet->getStyle($cell)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C00000']],
        ]);
    }

    private function applyRedStyle(Worksheet $sheet, string $cell): void
    {
        $sheet->getStyle($cell)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C00000']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    private function applyOffDayCellStyle(Worksheet $sheet, string $cell): void
    {
        $sheet->getStyle($cell)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDE9D9']],
        ]);
    }

    private function isWeekendOrConfiguredHoliday(Carbon $date): bool
    {
        if ($date->isWeekend()) {
            return true;
        }

        $specificDates = config('office_calendar.holidays', []);
        $specificDates = is_array($specificDates) ? $specificDates : [];

        $recurringDates = config('office_calendar.recurring_holidays', []);
        $recurringDates = is_array($recurringDates) ? $recurringDates : [];

        return in_array($date->toDateString(), $specificDates, true)
            || in_array($date->format('m-d'), $recurringDates, true);
    }
}
