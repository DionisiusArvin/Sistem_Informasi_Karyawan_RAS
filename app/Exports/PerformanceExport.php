<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PerformanceExport implements FromCollection, WithHeadings
{
    protected $results;

    public function __construct(Collection $results)
    {
        $this->results = $results;
    }

    public function collection()
    {
        return $this->results->map(function ($row) {

            // Ambil teks badge tanpa emoji (biar Excel bersih)
            $status = str_contains($row->badge, 'Top Performer')
                ? 'Top Performer'
                : 'Needs Improvement';

            return [
                'Nama'        => $row->name,
                'Total Tugas' => $row->total_tasks,
                'Skor KPI'    => $row->final_score,
                'Ranking'     => $row->rank,
                'Status'      => $status,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama', 'Total Tugas', 'Skor KPI', 'Ranking', 'Status'];
    }
}
