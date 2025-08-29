<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\DailyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $viewData = [];

        if ($user->role === 'manager') {
            $projects = Project::with('dailyTasks')->get();
            $chartData = $projects->map(fn($project) => $project->getProgressPercentage());
            $chartLabels = $projects->map(fn($project) => $project->name);

            $viewData = [
                'totalProjects' => $projects->count(),
                'tasksToValidate' => DailyTask::where('status', 'Menunggu Validasi')->count(),
                'chartData' => $chartData,
                'chartLabels' => $chartLabels,
            ];
        } 
        elseif ($user->role === 'kepala_divisi') {
            $tasks = Task::whereHas('divisions', function ($query) use ($user) {$query->where('divisions.id', $user->division_id);})->with('dailyTasks')->get();
            $dailyTaskStatusCounts = DailyTask::whereIn('task_id', $tasks->pluck('id'))
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $viewData = [
                'totalTasks' => $tasks->count(),
                'tasksToValidate' => $dailyTaskStatusCounts->get('Menunggu Validasi', 0),
                'statusCounts' => $dailyTaskStatusCounts,
            ];
        } 
        elseif ($user->role === 'staff') {
            $myTaskStatusCounts = DailyTask::where('assigned_to_staff_id', $user->id)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
            
            $viewData = [
                'tasksInProgress' => $myTaskStatusCounts->get('Belum Dikerjakan', 0) + $myTaskStatusCounts->get('Revisi', 0),
                'tasksToValidate' => $myTaskStatusCounts->get('Menunggu Validasi', 0),
                'tasksCompleted' => $myTaskStatusCounts->get('Selesai', 0),
                'statusCounts' => $myTaskStatusCounts,
            ];
        }

        return view('dashboard', $viewData);
    }
}