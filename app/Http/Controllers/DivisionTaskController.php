<?php

namespace App\Http\Controllers;

use App\Models\DailyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionTaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $tasks = DailyTask::with(['task.project', 'activities'])
        ->whereHas('task.divisions', function ($query) use ($user) {
            $query->where('divisions.id', $user->division_id);
        })
        ->where(function ($query) use ($user) {
            $query->whereNull('assigned_to_staff_id')   // semua staff bisa ambil
                  ->orWhere('assigned_to_staff_id', $user->id); // khusus ditugaskan ke staff ini
        })
        ->get();

        // ... query untuk $availableTasks tetap sama ...
        $availableTasks = DailyTask::whereHas('task.divisions', function ($query) use ($user) {
                                $query->where('divisions.id', $user->division_id);
                            })
                            ->whereNull('assigned_to_staff_id')
                            ->where('status', 'Tersedia')
                            ->get();

        
        // Modifikasi di sini: Ambil juga data 'activities'
        $myTasks = DailyTask::with(['task.project', 'activities'])
            ->where('assigned_to_staff_id', $user->id)
            ->get();

        return view('division-tasks.index', [
            'tasks' => $tasks,
            'availableTasks' => $availableTasks,
            'myTasks' => $myTasks
        ]);
    }
}