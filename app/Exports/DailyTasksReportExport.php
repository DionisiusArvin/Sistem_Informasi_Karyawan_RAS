<?php

namespace App\Exports;

use App\Models\DailyTask;
use App\Models\AdHocTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DailyTasksReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $mode, $date, $from, $to, $month, $year;

    public function __construct($mode, $date, $from, $to, $month, $year)
    {
        $this->mode  = $mode;
        $this->date  = $date;
        $this->from  = $from;
        $this->to    = $to;
        $this->month = $month;
        $this->year  = $year;
    }

    public function collection()
    {
        $user = Auth::user();

        // ================= DAILY TASK =================
        $dailyQuery = DailyTask::with(['assignedToStaff', 'task.project']);

        // ================= ADHOC TASK =================
        $adhocQuery = AdHocTask::with(['assignedTo']);

        // ================= FILTER TANGGAL =================
        $applyDateFilter = function ($query) {
            if ($this->mode === 'tanggal' && $this->date) {
                $query->whereDate('updated_at', Carbon::parse($this->date));
            }
            elseif ($this->mode === 'range' && $this->from && $this->to) {
                $query->whereBetween('updated_at', [
                    Carbon::parse($this->from)->startOfDay(),
                    Carbon::parse($this->to)->endOfDay(),
                ]);
            }
            elseif ($this->mode === 'bulan' && $this->month && $this->year) {
                $query->whereMonth('updated_at', $this->month)
                      ->whereYear('updated_at', $this->year);
            }
            elseif ($this->mode === 'tahun' && $this->year) {
                $query->whereYear('updated_at', $this->year);
            }
        };

        $applyDateFilter($dailyQuery);
        $applyDateFilter($adhocQuery);

        // ================= FILTER ROLE =================
        if ($user->role === 'kepala_divisi') {
            $dailyQuery->whereHas('task.divisions', function ($q) use ($user) {
                $q->where('divisions.id', $user->division_id);
            });

            $adhocQuery->whereHas('assignedTo', function ($q) use ($user) {
                $q->where('division_id', $user->division_id);
            });
        }
        elseif ($user->role === 'staff') {
            $dailyQuery->where('assigned_to_staff_id', $user->id);
            $adhocQuery->where('assigned_to_id', $user->id);
        }

        $dailyTasks = $dailyQuery->get();
        $adhocTasks = $adhocQuery->get();

        // ================= GABUNG =================
        $rows = new Collection();

        foreach ($dailyTasks as $task) {
            $rows->push([
                'nama'     => $task->name,
                'tipe'     => 'Tugas Harian',
                'project'  => $task->task->project->name ?? '-',
                'pegawai'  => $task->assignedToStaff->name ?? '-',
                'status'   => $task->status,
                'tanggal'  => $task->updated_at,
            ]);
        }

        foreach ($adhocTasks as $task) {
            $rows->push([
                'nama'     => $task->name,
                'tipe'     => 'Tugas Mendadak',
                'project'  => '-',
                'pegawai'  => $task->assignedTo->name ?? '-',
                'status'   => $task->status,
                'tanggal'  => $task->updated_at,
            ]);
        }

        return $rows;
    }

    // ✅ URUTAN SESUAI PERMINTAAN
    public function headings(): array
    {
        return [
            'Nama Tugas',
            'Tipe Tugas',
            'Proyek',
            'Pegawai',
            'Status',
            'Tanggal',
        ];
    }

    public function map($row): array
    {
        return [
            $row['nama'],
            $row['tipe'],
            $row['project'],
            $row['pegawai'],
            $row['status'],
            Carbon::parse($row['tanggal'])->format('Y-m-d H:i:s'),
        ];
    }
}
