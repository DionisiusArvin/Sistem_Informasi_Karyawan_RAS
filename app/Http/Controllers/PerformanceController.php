<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DailyTask;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PerformanceExport;

class PerformanceController extends Controller
{
    /* ===============================
     * HALAMAN FILTER
     * =============================== */
    public function index()
    {
        // semua user kecuali manager
        $employees = User::where('role', '!=', 'manager')->get();

        return view('performance.index', compact('employees'));
    }

    /* ===============================
     * HITUNG KPI
     * =============================== */
    public function calculate(Request $request)
    {
        $results   = $this->getKpiData($request);
        $employees = User::where('role', '!=', 'manager')->get();

        return view('performance.index', [
            'results'   => $results,
            'employees' => $employees,
            'period'    => (int) $request->period,
            'status'    => $request->status,
            'userId'    => $request->user_id,
        ]);
    }

    /* ===============================
     * EXPORT PDF
     * =============================== */
    public function exportPdf(Request $request)
    {
        $results = $this->getKpiData($request);

        $periodeLabel = match ((int) $request->period) {
            1  => 'KPI 1 Bulan',
            6  => 'KPI 6 Bulan',
            12 => 'KPI 12 Bulan',
            default => 'KPI',
        };

        return Pdf::loadView('performance.pdf', [
            'results'      => $results,
            'printedAt'    => now()->format('d M Y H:i'),
            'periodeLabel' => $periodeLabel,
        ])->setPaper('A4', 'portrait')
        ->download('kpi-karyawan.pdf');
    }

    /* ===============================
     * EXPORT EXCEL
     * =============================== */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PerformanceExport($this->getKpiData($request)),
            'kpi-karyawan.xlsx'
        );
    }

    /* ===============================
     * CORE KPI LOGIC (FIXED)
     * =============================== */
    private function getKpiData(Request $request)
    {
        $request->validate([
            'period'  => 'required|in:1,6,12',
            'status'  => 'required',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $period    = (int) $request->period;
        $startDate = now()->subMonths($period);
        $endDate   = now();

        // MINIMAL TASK SUPAYA VALID RANKING
        $minTasks = match ($period) {
            1  => 5,
            6  => 30,
            12 => 60,
            default => 5,
        };

        $query = DailyTask::with('staff')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', $request->status)
            ->whereHas('staff', fn ($q) => $q->where('role', '!=', 'manager'));

        if ($request->filled('user_id')) {
            $query->where('assigned_to_staff_id', $request->user_id);
        }

        $results = $query
            ->selectRaw('assigned_to_staff_id, COUNT(*) as total_tasks')
            ->groupBy('assigned_to_staff_id')
            ->get()
            ->map(function ($item) use ($period, $startDate, $endDate, $minTasks) {

                $userId     = $item->assigned_to_staff_id;
                $totalTasks = (int) $item->total_tasks;

                /* ========= PRODUKTIVITAS ========= */
                $productivity = min(100, ($totalTasks / ($period * 20)) * 100);

                /* ========= TREND ========= */
                $current  = $this->countTasks($userId, $startDate, $endDate);
                $previous = $this->countTasks(
                    $userId,
                    $startDate->copy()->subMonths($period),
                    $startDate
                );

                $trend = $previous == 0 ? 0 : (($current - $previous) / $previous) * 100;
                $trendScore = max(0, min(100, $trend));

                /* ========= DEADLINE ========= */
                $timeScore = $this->calculateTimeScore($userId, $startDate, $endDate);

                /* ========= FINAL KPI ========= */
                $finalScore =
                    ($productivity * 0.5) +
                    ($timeScore * 0.3) +
                    ($trendScore * 0.2);

                // ❗ HUKUM kalau tugas terlalu sedikit ((dihapus))
                if ($totalTasks < $minTasks) {
                    $finalScore *= 0.4; // penalti 60%
                }

                return (object) [
                    'user_id'     => $userId,
                    'name'        => optional($item->staff)->name ?? '-',
                    'total_tasks' => $totalTasks,
                    'final_score' => round($finalScore, 1),
                    'trend_icon'  => $trend >= 0 ? '📈' : '📉',
                    'trend_value' => round($trend, 1),
                ];
            });

        /* ================= RANKING FINAL ================= */
        $topScore = $results->max('final_score');

        return $results
            // PRIORITAS: cukup tugas dulu, baru skor
            ->sortByDesc(fn ($r) => [
                $r->total_tasks >= $minTasks ? 1 : 0,
                $r->final_score
            ])
            ->values()
            ->map(function ($row, $i) use ($topScore, $minTasks) {

                $row->rank = $i + 1;

                [$icon, $color] = match ($row->rank) {
                    1 => ['🥇', '#facc15'],
                    2 => ['🥈', '#9ca3af'],
                    3 => ['🥉', '#cd7f32'],
                    default => ['🏅', '#3b82f6'],
                };

                $row->rank_icon  = $icon;
                $row->rank_color = $color;

                // ❗ Top Performer hanya jika produktif
                $row->badge = (
                    $row->final_score == $topScore &&
                    $row->total_tasks >= $minTasks
                )
                    ? '🔥 Top Performer'
                    : '🥉 Needs Improvement';

                return $row;
            });
    }

    /* ===============================
     * HELPER: COUNT TASK
     * =============================== */
    private function countTasks($userId, $start, $end)
    {
        return DailyTask::where('assigned_to_staff_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /* ===============================
     * HELPER: DEADLINE SCORE
     * =============================== */
    private function calculateTimeScore($userId, $start, $end)
    {
        $tasks = DailyTask::where('assigned_to_staff_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('due_date')
            ->get();

        if ($tasks->count() === 0) return 0;

        $onTime = $tasks->filter(fn ($t) => $t->updated_at <= $t->due_date)->count();

        return round(($onTime / $tasks->count()) * 100, 1);
    }
}
