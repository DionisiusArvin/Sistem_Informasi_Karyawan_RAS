<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\AdminTask;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class AdminTaskController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('manage-admin-tasks') && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $user = Auth::user();   
        $filterType = $request->input('type', 'all'); // Default ke 'all'

        $query = AdminTask::query();

        // Terapkan filter berdasarkan tipe
        if ($filterType === 'project') {
            $query->whereNotNull('project_id');
        } elseif ($filterType === 'non-project') {
            $query->whereNull('project_id');
        }

        // Terapkan filter berdasarkan peran
        if ($user->role === 'manager') {
            $tasks = $query->with('assignedToAdmin', 'project')->latest()->get();
        } else { // Admin
            $tasks = $query->where('assigned_to_admin_id', $user->id)
                        ->with('assignedToAdmin', 'project')->latest()->get();
        }

        return view('admin-tasks.index', [
            'tasks' => $tasks,
            'filterType' => $filterType
        ]);
    }

    public function create()
    {
        if (!Gate::allows('manage-admin-tasks')){
            abort(403);
        }

        $admins = User::where('role', 'admin')->get();
        $projects = Project::latest()->get(); // Ambil data semua proyek

        return view('admin-tasks.create', [
            'admins' => $admins,
            'projects' => $projects, // Kirim data proyek ke view
        ]);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('manage-admin-tasks')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to_admin_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id', // Tambah validasi
        ]);

        AdminTask::create([
            'project_id' => $validated['project_id'], // Tambahkan project_id
            'name' => $validated['name'],
            'description' => $validated['description'],
            'assigned_to_admin_id' => $validated['assigned_to_admin_id'],
            'due_date' => $validated['due_date'],
            'assigned_by_manager_id' => Auth::id(),
            'status' => 'Belum Dikerjakan',
        ]);

        return redirect()->route('admin-tasks.index')->with('success', 'Tugas untuk admin berhasil dibuat.');
    }

    public function showUploadForm(AdminTask $adminTask)
    {
        // Pastikan hanya admin yang ditugaskan yang bisa upload
        if ($adminTask->assigned_to_admin_id !== Auth::id()) {
            abort(403);
        }
        return view('admin-tasks.upload', ['task' => $adminTask]);
    }

    public function handleUpload(Request $request, AdminTask $adminTask)
    {
        // dd($request->all());
        if ($adminTask->assigned_to_admin_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => 'nullable|file|max:10240', // max 10MB
            'link' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'status' => 'Selesai',
            'notes' => $validated['notes'] ?? null,
            'link' => $validated['link'] ?? null, // ✅ simpan link
        ];

        // Kalau ada file
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('admin_files', 'public');
        }

        $adminTask->update($data);

        return redirect()->route('admin-tasks.index')->with('success', 'Pekerjaan berhasil di-upload.');
    }


    public function edit(AdminTask $adminTask)
    {
        if (!Gate::allows('manage-admin-tasks')) {
            abort(403);
        }

        $admins = User::where('role', 'admin')->get();

        return view('admin-tasks.edit', [
            'task' => $adminTask,
            'admins' => $admins
        ]);
    }

    public function update(Request $request, AdminTask $adminTask)
    {
        if (!Gate::allows('manage-admin-tasks')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to_admin_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $adminTask->update($validated);

        return redirect()->route('admin-tasks.index')->with('success', 'Tugas admin berhasil diperbarui.');
    }

    public function destroy(AdminTask $adminTask)
    {
        if (!Gate::allows('manage-admin-tasks')) {
            abort(403);
        }

        $adminTask->delete();

        return redirect()->route('admin-tasks.index')->with('success', 'Tugas admin berhasil dihapus.');
    }

    public function showFile(AdminTask $adminTask)
    {
        $user = Auth::user();

        // hanya manager & kepala_divisi yg boleh lihat
        if (!in_array($user->role, ['manager', 'kepala_divisi'])) {
            abort(403);
        }

        return view('admin-tasks.show-file', [
            'task' => $adminTask
        ]);
    }

    public function downloadFile(AdminTask $adminTask)
{
    $user = Auth::user();

    // hanya manager & kepala_divisi yg boleh download
    if (!in_array($user->role, ['manager', 'kepala_divisi'])) {
        abort(403);
    }

    if (!$adminTask->file_path) {
        return back()->with('error', 'File tidak ditemukan.');
    }

    $filePath = storage_path('app/public/' . $adminTask->file_path);

    return response()->download($filePath, basename($filePath));
}


}
