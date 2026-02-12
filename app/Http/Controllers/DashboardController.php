<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\DailyTask;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $viewData = [];

        // ======================= MANAGER =======================
        if ($user->role === 'manager') {

            $projects = Project::with('dailyTasks')->get()->map(function ($p) {
            $p->progress = $p->getProgressPercentage();
            return $p;
            });
            $currentYear = now()->year;

            // Total omset tahun berjalan
            $totalOmset = Project::whereYear('start_date', $currentYear)
                ->sum('contract_value');

            // Hitung status project (paling akurat)
            $completedProjects = $projects->where('progress', '>=', 100)->count();

            $lateProjects = $projects->filter(function ($p) use ($today) {
                $endDate = Carbon::parse($p->end_date)->startOfDay();
                return $p->progress < 100 && $endDate->lt($today);
            })->count();

            $ongoingProjects = $projects->filter(function ($p) use ($today) {
                $endDate = Carbon::parse($p->end_date)->startOfDay();
                return $p->progress < 100 && $endDate->gte($today);
            })->count();


            $unfinishedProjects = $projects->where('progress', '<', 100);


            $chartData = $unfinishedProjects->pluck('progress')->values();


            $chartLabels = $unfinishedProjects->pluck('name')->values();


            $viewData = [
                'totalProjects'     => $projects->count(),
                'completedProjects' => $completedProjects,
                'ongoingProjects'   => $ongoingProjects,
                'lateProjects'      => $lateProjects,
                'totalOmset'        => $totalOmset,
                'tasksToValidate'   => DailyTask::where('status', 'Menunggu Validasi')->count(),
                'chartData'         => $chartData,
                'chartLabels'       => $chartLabels,
            ];
        }

        // =================== KEPALA DIVISI ======================
        elseif ($user->role === 'kepala_divisi') {

            $tasks = Task::whereHas('divisions', function ($q) use ($user) {
                $q->where('divisions.id', $user->division_id);
            })->with('dailyTasks')->get();

            $dailyTaskStatusCounts = DailyTask::whereIn('task_id', $tasks->pluck('id'))
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $jadwalDivisi = Schedule::where('kepala_divisi', $user->id)
                ->whereDate('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->get();

            $viewData = [
                'totalTasks'      => $tasks->count(),
                'tasksToValidate' => $dailyTaskStatusCounts->get('Menunggu Validasi', 0),
                'statusCounts'    => $dailyTaskStatusCounts,
                'jadwalDivisi'    => $jadwalDivisi,
            ];
        }

        // ======================= STAFF ==========================
        elseif ($user->role === 'staff') {

            $myTaskStatusCounts = DailyTask::where('assigned_to_staff_id', $user->id)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $viewData = [
                'tasksInProgress' =>
                    $myTaskStatusCounts->get('Belum Dikerjakan', 0) +
                    $myTaskStatusCounts->get('Revisi', 0),

                'tasksToValidate' => $myTaskStatusCounts->get('Menunggu Validasi', 0),
                'tasksCompleted'  => $myTaskStatusCounts->get('Selesai', 0),
                'statusCounts'    => $myTaskStatusCounts,
            ];
        }

        return view('dashboard', $viewData);
    }
}
