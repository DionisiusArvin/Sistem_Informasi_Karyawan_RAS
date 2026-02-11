<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TaskActivity;
use App\Models\AdHocTask; // ✅ TAMBAHAN
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PerformanceExport;

class PerformanceController extends Controller
{
    public function index()
    {
        $employees = User::where('role', '!=', 'manager')->get();
        return view('performance.index', compact('employees'));
    }

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

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PerformanceExport($this->getKpiData($request)),
            'kpi-karyawan.xlsx'
        );
    }

    /* ================= KPI LOGIC ================= */
    private function getKpiData(Request $request)
    {
        $request->validate([
            'period'  => 'required|in:1,6,12',
            'status'  => 'required',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $period    = (int) $request->period;
        $startDate = now()->subMonths($period)->startOfDay();
        $endDate   = now()->endOfDay();

        $employeesQuery = User::where('role', '!=', 'manager');
        if ($request->filled('user_id')) {
            $employeesQuery->where('id', $request->user_id);
        }
        $employees = $employeesQuery->get();

        $statusMap = [
            'selesai' => 'Selesai',
            'valid' => 'Menunggu Validasi',
        ];

        $statusKey = strtolower((string) $request->status);
        $statusValue = $statusKey === 'semua'
            ? null
            : ($statusMap[$statusKey] ?? $request->status);

        /* ================= DAILY TASK SCORE ================= */
        $activityQuery = TaskActivity::query()
            ->selectRaw('task_activities.user_id, COUNT(DISTINCT task_activities.daily_task_id) as total_tasks')
            ->selectRaw('SUM((daily_tasks.weight * task_activities.progress_percent) / 8) as total_score')
            ->join('daily_tasks', 'daily_tasks.id', '=', 'task_activities.daily_task_id')
            ->join('users', 'users.id', '=', 'task_activities.user_id')
            ->whereBetween('task_activities.created_at', [$startDate, $endDate])
            ->whereNotNull('task_activities.progress_percent')
            ->where('users.role', '!=', 'manager');

        if ($request->filled('user_id')) {
            $activityQuery->where('task_activities.user_id', $request->user_id);
        }

        if (!empty($statusValue)) {
            $activityQuery->where('daily_tasks.status', $statusValue);
        }

        $activityRows = $activityQuery
            ->groupBy('task_activities.user_id')
            ->get()
            ->keyBy('user_id');

        /* ================= GABUNG DAILY + ADHOC ================= */
        $results = $employees->map(function ($employee) use ($activityRows, $startDate, $endDate) {

            $row = $activityRows->get($employee->id);

            $dailyScore = (float) ($row->total_score ?? 0);
            $dailyTasks = (int) ($row->total_tasks ?? 0);

            // ✅ HITUNG ADHOC
            $adhocScore = AdHocTask::where('assigned_to_id', $employee->id)
                ->where('status', 'Selesai')
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('weight');

            $adhocTasks = AdHocTask::where('assigned_to_id', $employee->id)
                ->where('status', 'Selesai')
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->count();

            $totalScore = $dailyScore + $adhocScore;
            $totalTasks = $dailyTasks + $adhocTasks;

            return (object) [
                'user_id'     => $employee->id,
                'name'        => $employee->name,
                'total_tasks' => $totalTasks,
                'final_score' => round($totalScore, 2),
            ];
        });

        /* ================= RANKING ================= */
        $topScore = $results->max('final_score');

        return $results
            ->sortByDesc('final_score')
            ->values()
            ->map(function ($row, $i) use ($topScore) {
                $row->rank = $i + 1;

                [$icon, $color] = match ($row->rank) {
                    1 => ['🥇', '#facc15'],
                    2 => ['🥈', '#9ca3af'],
                    3 => ['🥉', '#cd7f32'],
                    default => ['🏆', '#3b82f6'],
                };

                $row->rank_icon  = $icon;
                $row->rank_color = $color;

                $row->badge = ($topScore > 0 && $row->final_score == $topScore)
                    ? '🔥 Top Performer'
                    : '🥉 Needs Improvement';

                return $row;
            });
    }
}
