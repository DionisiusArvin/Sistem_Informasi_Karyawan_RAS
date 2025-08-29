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
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'required|date',
        ]);
        // Hapus semua validasi terkait 'weight' dari sini
        DailyTask::create([
            'task_id' => $task->id,
            'name' => $validated['name'],
            'due_date' => $validated['due_date'],
            'status' => 'Tersedia',
        ]);
        return back()->with('success', 'Tugas harian berhasil ditambahkan.');
    }

    public function claim(DailyTask $dailyTask)
    {
        // Pastikan tugas masih tersedia
        if ($dailyTask->assigned_to_staff_id !== null) {
            return back()->with('error', 'Tugas ini sudah diambil oleh staff lain.');
        }

        $dailyTask->update([
            'assigned_to_staff_id' => Auth::id(),
            'status' => 'Belum Dikerjakan',
        ]);

        return back()->with('success', 'Anda berhasil mengambil tugas.');
    }

    public function showUploadForm(DailyTask $dailyTask)
    {
        // Pastikan hanya staff yang ditugaskan yang bisa upload
        if ($dailyTask->assigned_to_staff_id !== Auth::id()) {
            abort(403);
        }
        return view('daily-tasks.upload', ['dailyTask' => $dailyTask]);
    }

    public function handleUpload(Request $request, DailyTask $dailyTask)
    {
        if ($dailyTask->assigned_to_staff_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => 'nullable|file|mimes:pdf,jpg,png,dwg,zip|max:10240',
            'link_url' => 'required|url', // link sekarang wajib
            'notes' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('task_files', 'public');
        }

        TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id' => Auth::id(),
            'activity_type' => 'upload_pekerjaan',
            'notes' => $validated['notes'] ?? null,
            'file_path' => $filePath,
            'link_url' => $validated['link_url'], // sudah wajib
        ]);

        $dailyTask->update(['status' => 'Menunggu Validasi']);

        return redirect()->route('division-tasks.index')->with('success', 'Pekerjaan berhasil di-upload.');
    }

    public function approve(DailyTask $dailyTask)
    {
        if (! Gate::allows('validate-task', $dailyTask)) {
            abort(403);
        }
        
        $completionStatus = Carbon::now()->startOfDay()->lte(Carbon::parse($dailyTask->due_date))
            ? 'tepat_waktu'
            : 'terlambat';

        $dailyTask->update([
            'status' => 'Selesai',
            'completion_status' => $completionStatus,
            'progress' => 100, // <-- Tambahkan ini untuk set progress 100%
        ]);

        return back()->with('success', 'Pekerjaan telah disetujui.');
    }

    public function reject(Request $request, DailyTask $dailyTask)
    {
        // Anda bisa menambahkan Gate di sini juga
        if (! Gate::allows('validate-task', $dailyTask)) {
            abort(403);
        }
        // Mencatat alasan revisi (opsional, tapi sangat direkomendasikan)
        TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id' => Auth::id(),
            'activity_type' => 'permintaan_revisi',
            'notes' => $request->input('revision_notes', 'Revisi diperlukan.'), // Ambil catatan dari form
        ]);
        
        $dailyTask->update(['status' => 'Revisi']);

        return back()->with('success', 'Tugas telah dikembalikan untuk revisi.');
    }

    public function claimAndUpload(Request $request, DailyTask $dailyTask)
    {
        if (!Gate::allows('claim-task', $dailyTask)) {
            abort(403);
        }

        if ($dailyTask->assigned_to_staff_id !== null) {
            return back()->with('error', 'Tugas ini sudah diambil oleh orang lain.');
        }

        $validated = $request->validate([
            'file' => 'nullable|file|mimes:pdf,jpg,png,dwg,zip|max:10240',
            'link_url' => 'required|url',
            'notes' => 'nullable|string',
        ]);

        // Claim tugas
        $dailyTask->update([
            'assigned_to_staff_id' => Auth::id(),
            'status' => 'Menunggu Validasi',
        ]);

        // Simpan file jika ada
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('task_files', 'public');
        }

        // Simpan aktivitas
        TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id' => Auth::id(),
            'activity_type' => 'upload_pekerjaan',
            'notes' => $validated['notes'] ?? null,
            'file_path' => $filePath,
            'link_url' => $validated['link_url'],
        ]);

        return back()->with('success', 'Anda berhasil mengambil dan meng-upload pekerjaan.');
    }

    public function update(Request $request, DailyTask $dailyTask)
    {
        // hanya kepala_divisi yang bisa update
        if (Auth::user()->role !== 'kepala_divisi') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'required|date',
        ]);

        $dailyTask->update($validated);

        return back()->with('success', 'Tugas harian berhasil diperbarui.');
    }

    public function destroy(DailyTask $dailyTask)
    {
        if (Auth::user()->role !== 'kepala_divisi') {
            abort(403);
        }

        $dailyTask->delete();

        return back()->with('success', 'Tugas harian berhasil dihapus.');
    }

}
