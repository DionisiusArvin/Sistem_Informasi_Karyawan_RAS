<?php

namespace App\Exports;

use App\Models\AdminTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdminTasksReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected Carbon $date;
    protected ?string $userId;

    public function __construct(string $date, ?string $userId = null)
    {
        $this->date = Carbon::parse($date);
        $this->userId = $userId;
    }

    public function collection()
    {
        $user = Auth::user();

        $query = AdminTask::whereDate('updated_at', $this->date)
            ->with(['assignedToAdmin', 'project']);

        if ($user->role === 'manager') {
            if ($this->userId) {
                $query->where('assigned_to_admin_id', $this->userId);
            }
        } elseif ($user->role === 'admin') {
            $query->where('assigned_to_admin_id', $user->id);
        } else {
            return collect();
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Tugas',
            'Proyek',
            'Admin',
            'Status',
            'Tanggal Update',
        ];
    }

    public function map($adminTask): array
    {
        return [
            $adminTask->name,
            $adminTask->project->name ?? 'Non Proyek',
            $adminTask->assignedToAdmin->name ?? 'N/A',
            $adminTask->status,
            $adminTask->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
