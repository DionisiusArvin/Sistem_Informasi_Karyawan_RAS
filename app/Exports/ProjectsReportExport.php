<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ProjectsReportExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Project::with('tasks.dailyTasks')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Proyek',
            'Klien',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Sisa Waktu (Hari)',
            'Progress Penyelesaian (%)',
            'Kondisi',
        ];
    }

    /**
     * @param Project $project
     * @return array
     */
    public function map($project): array
    {
        $sisaWaktu = now()->startOfDay()->diffInDays(Carbon::parse($project->end_date)->startOfDay(), false);

        return [
            $project->name,
            $project->client_name,
            Carbon::parse($project->start_date)->format('Y-m-d'),
            Carbon::parse($project->end_date)->format('Y-m-d'),
            $sisaWaktu,
            $project->getProgressPercentage(),
            ucfirst($project->getHealthStatus()),
        ];
    }
}