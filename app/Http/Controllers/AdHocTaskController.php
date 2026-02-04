<?php

namespace App\Http\Controllers;

use App\Models\AdHocTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AdHocTaskController extends Controller
{
    /** ============================
     *  CREATE
     *  ============================ */
    public function create()
    {
        if (!Gate::allows('manage-ad-hoc-tasks')) {
            abort(403);
        }

        $users = User::where('role', '!=', 'manager')->get();
        return view('ad-hoc-tasks.create', compact('users'));
    }

    /** ============================
     *  STORE
     *  ============================ */
    public function store(Request $request)
    {
        if (!Gate::allows('manage-ad-hoc-tasks')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        AdHocTask::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to_id' => $validated['assigned_to_id'],
            'due_date' => $validated['due_date'],
            'assigned_by_id' => Auth::id(),
            'status' => 'Belum Dikerjakan',
        ]);

        return redirect()->route('ad-hoc-tasks.index')->with('success', 'Tugas mendadak berhasil dibuat.');
    }

    /** ============================
     *  INDEX
     *  ============================ */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            $tasks = AdHocTask::with(['assignedTo', 'assignedBy'])
                ->latest()->paginate(10);
        } elseif ($user->role === 'kepala_divisi') {
            $staffIds = User::where('division_id', $user->division_id)->pluck('id');
            $tasks = AdHocTask::where('assigned_by_id', $user->id)
                ->orWhereIn('assigned_to_id', $staffIds)
                ->with(['assignedTo', 'assignedBy'])
                ->latest()->paginate(10);
        } else {
            $tasks = AdHocTask::where('assigned_to_id', $user->id)
                ->with(['assignedTo', 'assignedBy'])
                ->latest()->paginate(10);
        }

        return view('ad-hoc-tasks.index', compact('tasks'));
    }

    /** ============================
     *  UPLOAD FORM
     *  ============================ */
    public function showUploadForm(AdHocTask $adHocTask)
    {
        if ($adHocTask->assigned_to_id !== Auth::id()) {
            abort(403);
        }

        return view('ad-hoc-tasks.upload', ['task' => $adHocTask]);
    }

    /** ============================
     *  HANDLE UPLOAD
     *  ============================ */
    public function handleUpload(Request $request, $id)
{
    $task = AdHocTask::findOrFail($id);

    $validated = $request->validate([
        'file_path' => 'nullable|file|max:102400', // maksimal 10MB
        'link' => 'nullable|url',
        'notes' => 'nullable|string|max:500',
    ]);

    // Pastikan minimal satu dari file/link diisi
    if (!$request->hasFile('file_path') && empty($request->link)) {
        return back()->withErrors(['file_path' => 'Harap unggah file atau isi link.'])->withInput();
    }

    // Simpan file jika ada
    if ($request->hasFile('file_path')) {
        $path = $request->file('file_path')->store('adhoc_files', 'public');
        $validated['file_path'] = $path;
    }

    // Update task
    $task->update([
        'status' => 'Selesai',
        'file_path' => $validated['file_path'] ?? null,
        'link' => $validated['link'] ?? null,
        'notes' => $validated['notes'] ?? null,
    ]);

    return redirect()
        ->route('ad-hoc-tasks.index')
        ->with('success', 'Tugas berhasil diselesaikan!');
}


    /** ============================
     *  DOWNLOAD FILE
     *  ============================ */
    public function downloadFile($id)
    {
        $task = AdHocTask::findOrFail($id);

        if (!$task->file_path) {
            return redirect()->back()->with('error', 'Tidak ada file untuk diunduh.');
        }

        $filePath = storage_path('app/public/' . $task->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($filePath, basename($task->file_path));
    }

    /** ============================
     *  EDIT
     *  ============================ */
    public function edit($id)
    {
        $task = AdHocTask::findOrFail($id);
        $users = User::all();
        return view('ad-hoc-tasks.edit', compact('task', 'users'));
    }

    /** ============================
     *  UPDATE
     *  ============================ */
    public function update(Request $request, $id)
    {
        $task = AdHocTask::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|string|in:Belum Dikerjakan,Proses,Selesai',
            'link' => 'nullable|url',
            'notes' => 'nullable|string',
            'assigned_to_id' => 'required|exists:users,id',
        ]);

        $task->update($validated);

        return redirect()->route('ad-hoc-tasks.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    /** ============================
     *  DELETE
     *  ============================ */
    public function destroy(AdHocTask $adHocTask)
    {
        if (!Gate::allows('manage-ad-hoc-tasks')) {
            abort(403);
        }

        $adHocTask->delete();

        return redirect()->route('ad-hoc-tasks.index')->with('success', 'Tugas mendadak berhasil dihapus.');
    }

    /** ============================
     *  UPLOAD SIMPLE (optional)
     *  ============================ */
    public function upload(Request $request, $id)
    {
        $task = AdHocTask::findOrFail($id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('adhoc_files', $filename, 'public');
            $task->file_path = 'adhoc_files/' . $filename;
        }

        if ($request->filled('link')) {
            $task->link = $request->input('link');
        }

        $task->save();

        return redirect()->back()->with('success', 'File berhasil diupload.');
    }
}
