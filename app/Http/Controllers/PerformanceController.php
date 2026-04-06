<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TaskActivity;
use App\Models\AdHocTask;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PerformanceExport;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type ?? 'staf';
        $employees = User::where('role', '!=', 'manager')->get();

        return view('performance.index', compact('employees', 'type'));
    }

    public function calculate(Request $request)
    {
        $type = $request->type ?? 'staf';
        $status = $this->normalizeStatusFilter($request->status);

        return view('performance.index', [
            'results'   => $this->getKpiData($request, $type),
            'employees' => User::where('role', '!=', 'manager')->get(),
            'period'    => (int) $request->period,
            'status'    => $status,
            'userId'    => $request->user_id,
            'type'      => $type,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $type = $request->type ?? 'staf';
        $period = (int) $request->period;
        $results = $this->getKpiData($request, $type);

        return Pdf::loadView('performance.pdf', [
            'results'   => $results,
            'type'      => $type,
            'periodeLabel' => $this->buildPeriodLabel($period),
            'printedAt' => now()->format('d M Y H:i'),
        ])->download($this->buildExportFilename('pdf', $type, $period));
    }

    public function exportExcel(Request $request)
    {
        $type = $request->type ?? 'staf';
        $period = (int) $request->period;
        $results = $this->getKpiData($request, $type);

        return Excel::download(
            new PerformanceExport($results),
            $this->buildExportFilename('xlsx', $type, $period)
        );
    }

    /* ================= KPI LOGIC ================= */
    /*
     * RUMUS PENILAIAN STAF TEKNIS: A × C × B
     * A = Mengerjakan (1) / Tidak Mengerjakan (0)
     * B = Konsistensi: Selesai (100%), Revisi (50%), Belum Dikerjakan (0%)
     * C = Bobot Pekerjaan (weight, min 1 max 10)
     *
     * KPI = Σ(A × C × B) untuk semua tugas
     *
     * KEPALA DIVISI:
     * 30% → Kapasitas Produksi (A×C×B dari project PIC)
     * 70% → Nilai Kepala Divisi (A×C×B dari tugas sendiri)
     */
    private function getKpiData(Request $request, $type = 'staf')
    {
        $statusFilter = $this->normalizeStatusFilter($request->status);

        $request->validate([
            'period'  => 'required|in:1,6,12',
            'status'  => 'required|in:semua,selesai,proses,valid',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $period    = (int) $request->period;
        $startDate = now()->subMonths($period)->startOfDay();
        $endDate   = now()->endOfDay();

        $employeesQuery = $type === 'kepala'
            ? User::where('role', 'kepala_divisi')
            : User::where('role', '!=', 'manager');

        if ($request->filled('user_id')) {
            $employeesQuery->where('id', $request->user_id);
        }

        $employees = $employeesQuery->get();

        /* ================= HELPER: Hitung skor A×C×B ================= */
        $calcAcb = function ($dailyTasks) {
            $score = 0;
            foreach ($dailyTasks as $dt) {
                // A = Mengerjakan (1) jika ada assigned_to_staff_id, 0 jika tidak
                $a = $dt->assigned_to_staff_id ? 1 : 0;

                // C = Bobot pekerjaan
                $c = (int) ($dt->weight ?? 1);

                // B = Konsistensi berdasarkan status
                // Hanya tugas yang 'Selesai' yang bobotnya dihitung (100%), selain itu 0%
                $b = match ($dt->status) {
                    'Selesai'  => 1.0,   // 100%
                    default    => 0.0,   // 0%
                };

                $score += $a * $c * $b;
            }
            return $score;
        };

        /* ================= LOOP KPI ================= */
        $results = $employees->map(function ($employee) use ($startDate, $endDate, $type, $calcAcb, $statusFilter) {

            if ($type === 'kepala') {
                /* =======================================================
                 * KPI KEPALA DIVISI
                 * 30% → Kapasitas Produksi (A×C×B dari project PIC)
                 * 70% → Nilai Kepala Divisi (A×C×B dari tugas sendiri)
                 * ======================================================= */

                // ── 30% KAPASITAS PRODUKSI (dari project PIC) ──
                $picProjectIds = \App\Models\Project::where('pic_id', $employee->id)
                    ->pluck('id');

                $picDailyTasks = collect();
                if ($picProjectIds->isNotEmpty()) {
                    $picDailyTasks = \App\Models\DailyTask::whereIn('project_id', $picProjectIds)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->get();
                }

                $picDailyTasks = $this->filterTasksByStatus($picDailyTasks, $statusFilter);

                $kapasitasProduksi = $calcAcb($picDailyTasks);

                // ── 70% NILAI KEPALA DIVISI (tugas sendiri) ──
                $ownDailyTasks = \App\Models\DailyTask::where('assigned_to_staff_id', $employee->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $ownDailyTasks = $this->filterTasksByStatus($ownDailyTasks, $statusFilter);

                $nilaiKepalaDivisi = $calcAcb($ownDailyTasks);

                // Total tugas (termasuk yang belum selesai)
                $totalTasks = $picDailyTasks->count()
                    + $ownDailyTasks->count();

                // ── FINAL SCORE KEPALA DIVISI ──
                $finalScore = ($kapasitasProduksi * 0.30) + ($nilaiKepalaDivisi * 0.70);

                return (object)[
                    'user_id'            => $employee->id,
                    'name'               => $employee->name,
                    'total_tasks'        => $totalTasks,
                    'kapasitas_produksi' => round($kapasitasProduksi, 2),
                    'nilai_kepala'       => round($nilaiKepalaDivisi, 2),
                    'final_score'        => round($finalScore, 2),
                ];

            } else {
                /* =======================================================
                 * KPI STAF TEKNIS
                 * Rumus: Σ(A × C × B) per tugas
                 * ======================================================= */

                $dailyTasks = \App\Models\DailyTask::where('assigned_to_staff_id', $employee->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $dailyTasks = $this->filterTasksByStatus($dailyTasks, $statusFilter);

                $finalScore = $calcAcb($dailyTasks);

                // Total tugas (termasuk yang belum selesai)
                $totalTasks = $dailyTasks->count();

                return (object)[
                    'user_id'     => $employee->id,
                    'name'        => $employee->name,
                    'total_tasks' => $totalTasks,
                    'final_score' => round($finalScore, 2),
                ];
            }
        });

        /* ================= RANKING ================= */
        $topScore = $results->max('final_score');
        $averageScore = $results->avg('final_score');

        return $results
            ->sortByDesc('final_score')
            ->values()
            ->map(function ($row, $i) use ($topScore, $averageScore) {

                $row->rank = $i + 1;

                if ($row->rank == 1) {
                    $row->rank_icon = '🥇';
                    $row->rank_color = '#facc15';
                } elseif ($row->rank == 2) {
                    $row->rank_icon = '🥈';
                    $row->rank_color = '#9ca3af';
                } elseif ($row->rank == 3) {
                    $row->rank_icon = '🥉';
                    $row->rank_color = '#cd7f32';
                } else {
                    $row->rank_icon = '🏆';
                    $row->rank_color = '#3b82f6';
                }

                if ($row->final_score == $topScore && $topScore > 0) {
                    $row->badge = '🔥 Top Performer';
                } elseif ($row->final_score >= $averageScore && $row->final_score > 0) {
                    $row->badge = '✨ Keren';
                } else {
                    $row->badge = '💪 Berjuang Lagi';
                }

                return $row;
            });
    }

    private function buildPeriodLabel(int $period): string
    {
        return match ($period) {
            1 => 'Periode 1 Bulan',
            6 => 'Periode 6 Bulan',
            12 => 'Periode 12 Bulan',
            default => 'Periode Kustom',
        };
    }

    private function buildExportFilename(string $extension, string $type, int $period): string
    {
        return sprintf(
            'kpi-%s-%s-bulan.%s',
            $type,
            $period ?: 'custom',
            $extension
        );
    }

    private function normalizeStatusFilter(?string $status): string
    {
        return match ($status) {
            'valid' => 'proses',
            'selesai', 'proses' => $status,
            default => 'semua',
        };
    }

    private function filterTasksByStatus($dailyTasks, string $statusFilter)
    {
        return match ($statusFilter) {
            'selesai' => $dailyTasks->filter(fn ($task) => $task->status === 'Selesai')->values(),
            'proses' => $dailyTasks->filter(function ($task) {
                $progress = (int) ($task->progress ?? 0);

                return $task->status === 'Revisi' || $progress < 100;
            })->values(),
            default => $dailyTasks,
        };
    }
}
