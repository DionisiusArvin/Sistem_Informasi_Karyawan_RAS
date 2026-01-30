<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyTask;
use App\Models\TaskActivity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ValidationController extends Controller
{
    public function index()
    {
        $tasks = DailyTask::with([
            'task.project',
            'assignedToStaff',
            'activities'
        ])
        ->where('status', 'Menunggu Validasi')
        ->latest()
        ->get();

        return view('validation.index', compact('tasks'));
    }

    public function approve($id)
    {
        $task = DailyTask::findOrFail($id);

        $completionStatus = Carbon::now()->startOfDay()
            ->lte(Carbon::parse($task->due_date))
            ? 'tepat_waktu'
            : 'terlambat';

        $task->update([
            'status' => 'Selesai',
            'completion_status' => $completionStatus,
        ]);

        return back()->with('success', 'Tugas harian berhasil divalidasi.');
    }

    public function reject(Request $request, $id)
    {
        $task = DailyTask::findOrFail($id);

        $request->validate([
            'revision_notes' => 'required|string'
        ]);

        $task->update([
            'status' => 'Revisi'
        ]);

        TaskActivity::create([
            'daily_task_id' => $task->id,
            'user_id' => Auth::id(),
            'activity_type' => 'permintaan_revisi',
            'notes' => $request->revision_notes
        ]);

        return back()->with('success', 'Catatan revisi berhasil dikirim.');
    }

    public function continue(Request $request, $id)
    {
        $task = DailyTask::findOrFail($id);

        $request->validate([
            'notes' => 'nullable|string'
        ]);

        $task->update([
            'status' => 'Lanjutkan',
        ]);

        TaskActivity::create([
            'daily_task_id' => $task->id,
            'user_id' => Auth::id(),
            'activity_type' => 'lanjutkan_tugas',
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Tugas berhasil dilanjutkan.');
    }
}
