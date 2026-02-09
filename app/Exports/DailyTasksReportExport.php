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

    $query = DailyTask::whereDate('updated_at', $this->date)
        ->with(['assignedToStaff', 'task.project']);

    if ($user->role === 'manager') {
        // Manager bisa lihat semua staff & kepala divisi
        // (tidak ada filter tambahan)
    } elseif ($user->role === 'kepala_divisi') {
        // Kepala divisi hanya bisa lihat staff di divisinya
        $query->whereHas('task.divisions', function ($q) use ($user) {
            $q->where('divisions.id', $user->division_id);
        });
    } elseif ($user->role === 'staff') {
        // Staff hanya lihat miliknya sendiri
        $query->where('assigned_to_staff_id', $user->id);
    }

    return $query->get();
}



    public function headings(): array
    {
        return [
            'Nama Tugas',
            'Proyek',
            'Nama Staff',
            'Status',
            'Penyelesaian',
            'Tanggal Update',
        ];
    }

    public function map($dailyTask): array
    {
        return [
            $dailyTask->name,
            $dailyTask->task->project->name,
            $dailyTask->assignedToStaff->name ?? 'N/A',             
            $dailyTask->status,
            ucfirst(str_replace('_', ' ', $dailyTask->completion_status)),
            $dailyTask->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}