<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyTask;
use App\Models\TaskActivity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\NotificationService;

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
        $dailyTask = DailyTask::findOrFail($id);

        $completionStatus = Carbon::now()->startOfDay()
            ->lte(Carbon::parse($dailyTask->due_date))
            ? 'tepat_waktu'
            : 'terlambat';

        $dailyTask->update([
            'status' => 'Selesai',
            'completion_status' => $completionStatus,
            'progress' => 100,
        ]);

        $dailyTask->task->update([
            'progress' => 100,
        ]);

        // ✅ NOTIF KE STAFF
        NotificationService::send(
            $dailyTask->assigned_to_staff_id,
            'Tugas Harian Anda Telah Divalidasi',
            'Tugas "' . $dailyTask->name . '" telah disetujui',
            route('division-tasks.index') . '#task-' . $dailyTask->id
        );

        return back()->with('success', 'Tugas harian berhasil divalidasi.');
    }

    public function reject(Request $request, $id)
    {
        $dailyTask = DailyTask::findOrFail($id);

        $request->validate([
            'revision_notes' => 'required|string'
        ]);

        $dailyTask->update([
            'status' => 'Revisi'
        ]);

        TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id' => Auth::id(),
            'activity_type' => 'permintaan_revisi',
            'notes' => $request->revision_notes
        ]);

        // ✅ NOTIF KE STAFF
        NotificationService::send(
            $dailyTask->assigned_to_staff_id,
            'Tugas Harian Anda Direvisi',
            'Tugas "' . $dailyTask->name . '" perlu revisi',
            route('division-tasks.index') . '#task-' . $dailyTask->id
        );

        return back()->with('success', 'Catatan revisi berhasil dikirim.');
    }

    public function continue(Request $request, $id)
    {
        $dailyTask = DailyTask::findOrFail($id);

        $request->validate([
            'notes' => 'nullable|string'
        ]);

        $dailyTask->update([
            'status' => 'Lanjutkan',
        ]);

        TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id' => Auth::id(),
            'activity_type' => 'lanjutkan_tugas',
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Tugas berhasil dilanjutkan.');
    }
}
