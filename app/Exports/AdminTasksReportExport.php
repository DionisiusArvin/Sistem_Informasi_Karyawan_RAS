<?php

namespace App\Exports;

use App\Models\AdminTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell
};
use Maatwebsite\Excel\Events\AfterSheet;

class AdminTasksReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell
{
    protected string $mode;
    protected ?Carbon $date;
    protected ?Carbon $from;
    protected ?Carbon $to;
    protected ?int $month;
    protected ?int $year;
    protected ?string $userId;

    public function __construct(
        string $mode,
        ?string $date = null,
        ?string $from = null,
        ?string $to = null,
        ?int $month = null,
        ?int $year = null,
        ?string $userId = null
    ) {
        $this->mode   = $mode;
        $this->date   = $date ? Carbon::parse($date) : null;
        $this->from   = $from ? Carbon::parse($from) : null;
        $this->to     = $to ? Carbon::parse($to) : null;
        $this->month  = $month;
        $this->year   = $year;
        $this->userId = $userId;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', $this->getJudulText());
                $event->sheet->setCellValue('A2', $this->getPeriodeText());

                $event->sheet->mergeCells('A1:E1');
                $event->sheet->mergeCells('A2:E2');

                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A2')->getFont()->setItalic(true);
            },
        ];
    }

    private function getJudulText(): string
    {
        return match ($this->mode) {
            'tanggal' => 'LAPORAN TUGAS ADMIN TANGGAL ' .
                $this->date?->translatedFormat('d F Y'),

            'range' => 'LAPORAN TUGAS ADMIN ' .
                $this->from?->translatedFormat('d M Y') .
                ' s/d ' .
                $this->to?->translatedFormat('d M Y'),

            'bulan' => 'LAPORAN TUGAS ADMIN BULAN ' .
                Carbon::create()->month($this->month)->translatedFormat('F') .
                " {$this->year}",

            'tahun' => "LAPORAN TUGAS ADMIN TAHUN {$this->year}",

            default => 'LAPORAN TUGAS ADMIN',
        };
    }

    private function getPeriodeText(): string
    {
        return match ($this->mode) {
            'tanggal' => 'Periode: ' .
                $this->date?->translatedFormat('d F Y'),

            'range' => 'Periode: ' .
                $this->from?->translatedFormat('d F Y') .
                ' s/d ' .
                $this->to?->translatedFormat('d F Y'),

            'bulan' => 'Periode: Bulan ' .
                Carbon::create()->month($this->month)->translatedFormat('F') .
                " Tahun {$this->year}",

            'tahun' => "Periode: Tahun {$this->year}",

            default => '',
        };
    }

    public function collection()
    {
        $user = Auth::user();

        $query = AdminTask::with(['assignedToAdmin', 'project']);

        // === FILTER PERIODE ===
        match ($this->mode) {
            'tanggal' => $query->whereDate('updated_at', $this->date),
            'range'   => $query->whereBetween('updated_at', [
                $this->from?->startOfDay(),
                $this->to?->endOfDay()
            ]),
            'bulan'   => $query->whereMonth('updated_at', $this->month)
                               ->whereYear('updated_at', $this->year),
            'tahun'   => $query->whereYear('updated_at', $this->year),
            default   => null,
        };

        // === FILTER ROLE ===
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
