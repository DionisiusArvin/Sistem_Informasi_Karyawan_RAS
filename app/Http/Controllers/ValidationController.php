<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyTask;
use App\Models\TaskActivity;
use Illuminate\Support\Facades\Auth;

class ValidationController extends Controller
{
    public function index()
    {
        // Menampilkan semua tugas harian yang menunggu validasi
        $tasks = DailyTask::with('assignedToStaff')
            ->where('status', 'Menunggu Validasi')
            ->get();

        return view('validation.index', compact('tasks'));
    }

    public function approve($id)
    {
        $task = DailyTask::findOrFail($id);

        $task->update([
            'status' => 'Selesai'
        ]);

        return redirect()->back()->with('success', 'Tugas harian berhasil divalidasi.');
    }

    public function reject(Request $request, $id)
    {
        $task = DailyTask::findOrFail($id);

        // validasi input
        $request->validate([
            'revision_notes' => 'required|string'
        ]);

        // ubah status task
        $task->update([
            'status' => 'Revisi'
        ]);

        // simpan catatan revisi sebagai aktivitas
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

        // simpan catatan "lanjutkan" ke aktivitas
        TaskActivity::create([
            'daily_task_id' => $task->id,
            'user_id' => Auth::id(),
            'activity_type' => 'lanjutkan_tugas',
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil dilanjutkan.');
    }
}
