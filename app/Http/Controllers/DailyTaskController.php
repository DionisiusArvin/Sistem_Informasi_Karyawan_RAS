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
        $users = User::whereIn('role', ['staff', 'kepala_divisi'])->get();

        return view('daily-tasks.create', [
            'task' => $task,
            'users' => $users,
        ]);
    }

    /* ================= STORE ================= */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_item_id' => 'required|exists:project_items,id',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
            'assigned_to_staff_id' => 'nullable|exists:users,id',
        ]);

        DailyTask::create([
            'task_id' => $task->id,
            'project_id' => $task->project_id,
            'project_item_id' => $validated['project_item_id'],
            'name' => \App\Models\ProjectItem::find($validated['project_item_id'])->name, // 🔥 ini penting
            'due_date' => $validated['due_date'],
            'description' => $validated['description'] ?? null,
            'assigned_to_staff_id' => $validated['assigned_to_staff_id'] ?? null,
            'status' => 'Belum Dikerjakan',
            'progress' => 0,
        ]);

        return back()->with('success', 'Daily Task berhasil dibuat.');
    }

    /* ================= CLAIM ================= */
    public function claim(DailyTask $dailyTask)
    {
        if ($dailyTask->status !== 'open') {
            return back();
        }

        $dailyTask->update([
            'status' => 'taken',
            'taken_by' => auth()->id(),
            'taken_at' => now(),
        ]);

        return back();
    }

    /* ================= UPLOAD FORM ================= */
    public function showUploadForm(DailyTask $dailyTask)
    {
        return $this->uploadForm($dailyTask);
    }

    public function uploadForm(DailyTask $dailyTask)
    {
        if ($dailyTask->assigned_to_staff_id !== Auth::id()) {
            abort(403);
        }

        return view('daily-tasks.upload', compact('dailyTask'));
    }

    /* ================= HANDLE UPLOAD ================= */
    public function handleUpload(Request $request, DailyTask $dailyTask)
    {
        return $this->upload($request, $dailyTask);
    }

    /* ================= UPLOAD ================= */
    public function upload(Request $request, DailyTask $dailyTask)
    {
        if ($dailyTask->assigned_to_staff_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg,zip,xls,xlsx|max:102400',
            'link_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        if (!$request->hasFile('file') && !$request->filled('link_url')) {
            return back()
                ->withErrors(['file' => 'Harus upload file atau isi link salah satu.'])
                ->withInput();
        }

        $filePath = $request->hasFile('file')
            ? $request->file('file')->store('task_files', 'public')
            : null;

        TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id' => Auth::id(),
            'activity_type' => 'upload_pekerjaan',
            'notes' => $validated['notes'] ?? null,
            'file_path' => $filePath,
            'link_url' => $validated['link_url'] ?? null,
        ]);

        $dailyTask->update([
            'status' => 'Menunggu Validasi',
        ]);

        return redirect()
            ->route('division-tasks.index')
            ->with('success', 'Upload pekerjaan berhasil, menunggu validasi.');
    }

    /* ================= CLAIM + UPLOAD ================= */
    public function claimAndUpload(Request $request, DailyTask $dailyTask)
    {
        if (!Gate::allows('claim-task', $dailyTask)) {
            abort(403);
        }

        if ($dailyTask->assigned_to_staff_id !== null) {
            return back()->with('error', 'Tugas ini sudah diambil orang lain.');
        }

        $dailyTask->update([
            'assigned_to_staff_id' => Auth::id(),
        ]);

        return $this->upload($request, $dailyTask);
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

        $dailyTask->update(['status' => 'Revisi']);

        return back()->with('success', 'Tugas dikembalikan untuk revisi.');
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, DailyTask $dailyTask)
    {
        if (Auth::user()->role !== 'kepala_divisi') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'assigned_to_staff_id' => 'nullable|exists:users,id',
            'keterangan' => 'nullable|string',
        ]);

        $dailyTask->update($validated);

        return back()->with('success', 'Daily task berhasil diperbarui.');
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
}
