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

        // ... query untuk $availableTasks tetap sama ...
        $availableTasks = DailyTask::whereHas('task.divisions', function ($query) use ($user) {
            $query->where('divisions.id', $user->division_id);
        })
        ->whereNull('assigned_to_staff_id')
        ->where('status', 'Tersedia')
        ->get();

        
        // Modifikasi di sini: Ambil juga data 'activities'
        $myTasks = DailyTask::where('assigned_to_staff_id', $user->id)
                            ->with('activities') // <-- Tambahkan ini
                            ->get();

        return view('division-tasks.index', [
            'availableTasks' => $availableTasks,
            'myTasks' => $myTasks
        ]);
    }
}