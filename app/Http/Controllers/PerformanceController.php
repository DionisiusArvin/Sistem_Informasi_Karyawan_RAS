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

        return view('performance.index', [
            'results'   => $this->getKpiData($request, $type),
            'employees' => User::where('role', '!=', 'manager')->get(),
            'period'    => (int) $request->period,
            'status'    => $request->status,
            'userId'    => $request->user_id,
            'type'      => $type,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $type = $request->type ?? 'staf';

        return Pdf::loadView('performance.pdf', [
            'results'   => $this->getKpiData($request, $type),
            'printedAt' => now()->format('d M Y H:i'),
        ])->download('kpi-karyawan.pdf');
    }

    public function exportExcel(Request $request)
    {
        $type = $request->type ?? 'staf';

        return Excel::download(
            new PerformanceExport($this->getKpiData($request, $type)),
            'kpi-karyawan.xlsx'
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
        $request->validate([
            'period'  => 'required|in:1,6,12',
            'status'  => 'required',
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

                // B = Konsistensi berdasarkan status / progress
                $b = match ($dt->status) {
                    'Selesai'  => 1.0,   // 100%
                    'Revisi'   => 0.5,   // 50%
                    default    => ($dt->progress ?? 0) / 100.0, // berdasarkan progress saat ini
                };

                $score += $a * $c * $b;
            }
            return $score;
        };

        /* ================= LOOP KPI ================= */
        $results = $employees->map(function ($employee) use ($startDate, $endDate, $type, $calcAcb) {

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

                $kapasitasProduksi = $calcAcb($picDailyTasks);

                // ── 70% NILAI KEPALA DIVISI (tugas sendiri) ──
                $ownDailyTasks = \App\Models\DailyTask::where('assigned_to_staff_id', $employee->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

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

        return $results
            ->sortByDesc('final_score')
            ->values()
            ->map(function ($row, $i) use ($topScore) {

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

                $row->badge = ($row->final_score == $topScore && $topScore > 0)
                    ? '🔥 Top Performer'
                    : 'Perlu Improvement';

                return $row;
            });
    }
}