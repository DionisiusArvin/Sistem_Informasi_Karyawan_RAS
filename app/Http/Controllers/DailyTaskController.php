<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\DailyTask;
use App\Models\TaskActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DailyTaskController extends Controller
{
    /* ================= CREATE FORM ================= */
    public function create(Task $task)
    {
        return view('daily-tasks.create', [
            'task' => $task,
        ]);
    }

    /* ================= STORE ================= */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_item_id' => 'required|exists:project_items,id',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        DailyTask::create([
            'task_id' => $task->id,
            'project_id' => $task->project_id,
            'project_item_id' => $validated['project_item_id'],
            'name' => \App\Models\ProjectItem::find($validated['project_item_id'])->name,
            'due_date' => $validated['due_date'],
            'description' => $validated['description'] ?? null,
            'status' => 'Belum Dikerjakan',
            'progress' => 0,
        ]);

        return back()->with('success', 'Daily Task berhasil dibuat.');
    }

    /* ================= UPLOAD FORM ================= */
    public function uploadForm(DailyTask $dailyTask)
    {
        return view('daily-tasks.upload', compact('dailyTask'));
    }

    /* ================= HANDLE UPLOAD ================= */
    public function handleUpload(Request $request, DailyTask $dailyTask)
    {
        $request->validate([
            'file' => 'nullable|file|max:10240',
            'link_url' => 'nullable|url',
            'notes' => 'nullable|string'
        ]);

        if (!$request->file && !$request->link_url) {
            return back()->withErrors([
                'file' => 'Isi minimal file atau link.'
            ]);
        }

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('task_uploads', 'public');
        }

        // 🔥 SIMPAN AKTIVITAS
        \App\Models\TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id'       => auth()->id(),
            'activity_type' => 'upload_pekerjaan',
            'file_path'     => $filePath,
            'link_url'      => $request->link_url,
            'notes'         => $request->notes,
        ]);

        // 🔥 INI YANG SELAMA INI HILANG
        $dailyTask->update([
            'status' => 'Menunggu Validasi'
        ]);

        return redirect()
            ->route('division-tasks.index')
            ->with('success', 'Pekerjaan berhasil diupload, menunggu validasi.');
    }


    /* ================= APPROVE ================= */
    public function approve(DailyTask $dailyTask)
    {
        if (!Gate::allows('validate-task', $dailyTask)) {
            abort(403);
        }

        $completionStatus = Carbon::now()->startOfDay()
            ->lte(Carbon::parse($dailyTask->due_date))
            ? 'tepat_waktu'
            : 'terlambat';

        $dailyTask->update([
            'status' => 'Selesai',
            'completion_status' => $completionStatus,
            'progress' => 100,
        ]);

        return back()->with('success', 'Pekerjaan disetujui.');
    }

    /* ================= REJECT ================= */
    public function reject(Request $request, DailyTask $dailyTask)
    {
        if (!Gate::allows('validate-task', $dailyTask)) {
            abort(403);
        }

        TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id' => Auth::id(),
            'activity_type' => 'permintaan_revisi',
            'notes' => $request->input('revision_notes', 'Revisi diperlukan.'),
        ]);

        $dailyTask->update([
            'status' => 'Revisi'
        ]);

        return back()->with('success', 'Tugas dikembalikan untuk revisi.');
    }

    /* ================= DELETE ================= */
    public function destroy(DailyTask $dailyTask)
    {
        if (Auth::user()->role !== 'kepala_divisi') {
            abort(403);
        }

        $dailyTask->delete();

        return back()->with('success', 'Tugas harian dihapus.');
    }

    /* ================= DOWNLOAD ================= */
    public function download(DailyTask $dailyTask)
    {
        $lastUpload = $dailyTask->activities()
            ->where('activity_type', 'upload_pekerjaan')
            ->latest()
            ->first();

        if (!$lastUpload || !$lastUpload->file_path) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download(
            storage_path('app/public/' . $lastUpload->file_path)
        );
    }
    /* ================= UPDATE ================= */
    public function update(Request $request, DailyTask $dailyTask)
    {
    if (auth()->user()->role !== 'kepala_divisi') {
        abort(403);
    }

    $validated = $request->validate([
        'project_item_id' => 'required|exists:project_items,id',
        'due_date' => 'required|date',
        'description' => 'nullable|string',
    ]);

    $dailyTask->update([
        // name ikut item pekerjaan (biar konsisten)
        'project_item_id' => $validated['project_item_id'],
        'name' => \App\Models\ProjectItem::find($validated['project_item_id'])->name,
        'due_date' => $validated['due_date'],
        'description' => $validated['description'] ?? null,
    ]);

    return back()->with('success', 'Daily Task berhasil diperbarui.');
    }

    /* ================= CLAIM ================= */
    public function take(DailyTask $dailyTask)
    {
    if ($dailyTask->assigned_to_staff_id !== null) {
        return back()->with('error', 'Tugas sudah diambil.');
    }

    $dailyTask->update([
        'assigned_to_staff_id' => auth()->id(),
        'status' => 'Belum Dikerjakan',
    ]);

    return back()->with('success', 'Tugas berhasil diambil.');
    }
    /* ================= SHOW UPLOAD FORM ================= */
    public function showUploadForm(DailyTask $dailyTask)
    {
    return view('daily-tasks.upload', compact('dailyTask'));
    }


}
