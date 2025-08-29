<?php

namespace App\Exports;

use App\Models\DailyTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DailyTasksReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date;

    public function __construct(string $date)
    {
        $this->date = Carbon::parse($date);
    }

    public function collection()
    {
        $user = Auth::user();

        return DailyTask::whereHas('task.divisions', function ($query) use ($user) {
                $query->where('divisions.id', $user->division_id);
            })
            ->whereDate('updated_at', $this->date)
            ->with(['assignedToStaff', 'task.project'])
            ->get();
    }


    public function headings(): array
    {
        return [
            'Nama Staff',
            'Proyek',
            'Kode Proyek',
            'Tugas Harian',
            'Status',
            'Penyelesaian',
            'Tanggal Update',
        ];
    }

    public function map($dailyTask): array
    {
        return [
            $dailyTask->assignedToStaff->name ?? 'N/A',
            $dailyTask->task->project->name,
            $dailyTask->task->project->kode_proyek,
            $dailyTask->name,
            $dailyTask->status,
            ucfirst(str_replace('_', ' ', $dailyTask->completion_status)),
            $dailyTask->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}