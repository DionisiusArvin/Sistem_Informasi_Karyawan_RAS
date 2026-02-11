<?php

namespace App\Http\Controllers;

use App\Models\DailyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

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
                $query->whereNull('assigned_to_staff_id')
                      ->orWhere('assigned_to_staff_id', $user->id);
            })
            ->get();

        $availableTasks = DailyTask::whereHas('task.divisions', function ($query) use ($user) {
                                $query->where('divisions.id', $user->division_id);
                            })
                            ->whereNull('assigned_to_staff_id')
                            ->where('status', 'Tersedia')
                            ->get();

        $myTasks = DailyTask::with(['task.project', 'activities'])
            ->where('assigned_to_staff_id', $user->id)
            ->get();

        return view('division-tasks.index', [
            'tasks' => $tasks,
            'availableTasks' => $availableTasks,
            'myTasks' => $myTasks
        ]);
    }

    public function takeTask($id)
    {
        $task = DailyTask::with('task')->findOrFail($id);

        // Assign ke staff
        $task->assigned_to_staff_id = Auth::id();
        $task->status = 'Diproses';
        $task->save();

        // ✅ NOTIF PAKAI SERVICE
NotificationService::send(
    Auth::id(),
    'Tugas Berhasil Diambil',
    'Anda mengambil tugas: ' . $task->task->judul,
    route('division-tasks.index') . '#task-' . $task->id
);


        return back()->with('success', 'Tugas berhasil diambil');
    }
}
