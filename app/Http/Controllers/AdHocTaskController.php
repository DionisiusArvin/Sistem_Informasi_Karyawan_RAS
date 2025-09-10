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
    public function create()
    {
        if (! Gate::allows('manage-ad-hoc-tasks')) {
            abort(403);
        }

        // Ambil semua user yang bisa diberi tugas (bukan manager)
        $users = User::where('role', '!=', 'manager')->get();

        return view('ad-hoc-tasks.create', ['users' => $users]);
    }

    public function store(Request $request)
    {
        if (! Gate::allows('manage-ad-hoc-tasks')) {
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
            'description' => $validated['description'],
            'assigned_to_id' => $validated['assigned_to_id'],
            'due_date' => $validated['due_date'],
            'assigned_by_id' => Auth::id(),
            'status' => 'Belum Dikerjakan',
        ]);

        return redirect()->route('ad-hoc-tasks.index')->with('success', 'Tugas mendadak berhasil dibuat.');
    }
    
    public function index()
{
    $user = Auth::user();

    if ($user->role === 'manager') {
        $tasks = AdHocTask::with(['assignedTo', 'assignedBy'])
            ->latest()
            ->paginate(10); // ✅ paginate
    } elseif ($user->role === 'kepala_divisi') {
        $staffIds = User::where('division_id', $user->division_id)->pluck('id');
        $tasks = AdHocTask::where('assigned_by_id', $user->id)
            ->orWhereIn('assigned_to_id', $staffIds)
            ->with(['assignedTo', 'assignedBy'])
            ->latest()
            ->paginate(10); // ✅ paginate
    } else { // Staff & Admin
        $tasks = AdHocTask::where('assigned_to_id', $user->id)
            ->with(['assignedTo', 'assignedBy'])
            ->latest()
            ->paginate(10); // ✅ paginate
    }

    return view('ad-hoc-tasks.index', compact('tasks'));
}


    public function showUploadForm(AdHocTask $adHocTask)
    {
        if ($adHocTask->assigned_to_id !== Auth::id()) {
            abort(403);
        }
        return view('ad-hoc-tasks.upload', ['task' => $adHocTask]);
    }

    public function handleUpload(Request $request, AdHocTask $adHocTask)
{
    if (auth()->id() !== $adHocTask->assigned_to_id && auth()->id() !== $adHocTask->assigned_by_id) {
        abort(403);
    }

    $validated = $request->validate([
        'file_path' => 'nullable|file|max:10240',
        'link' => 'nullable|url',
        'notes' => 'nullable|string',
    ]);

    $filePath = null;
    if ($request->hasFile('file_path')) {
        $filePath = $request->file('file_path')->store('adhoc_files', 'public');
    }

    $adHocTask->update([
        'status'    => 'Selesai',
        'file_path' => $filePath,
        'notes'     => $validated['notes'] ?? null,
        'link'      => $validated['link'] ?? null,
    ]);

    return redirect()->route('ad-hoc-tasks.index')->with('success', 'Tugas mendadak berhasil di-upload.');
}
public function downloadFile($id)
{
    $task = AdHocTask::findOrFail($id);

    if (!$task->file_path) {
    return response()->download($filePath, basename($task->file_path));
}

$filePath = storage_path('app/public/'.$task->file_path);


    if (!file_exists($filePath)) {
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    return response()->download($filePath, $task->file);
}


public function destroy(AdHocTask $adHocTask)
    {
        if (!Gate::allows('manage-ad-hoc-tasks')) {
            abort(403);
        }

        $adHocTask->delete();

        return redirect()->route('ad-hoc-tasks.index')->with('success', 'Tugas Mendadak berhasil dihapus.');
    }

    public function edit($id)
{
    $task = AdHocTask::findOrFail($id);
    $users = User::all(); // ambil semua user untuk pilihan ditugaskan

    return view('ad-hoc-tasks.edit', compact('task', 'users'));
}

    public function update(Request $request, $id)
{
    $task = AdHocTask::findOrFail($id);

    $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date'    => 'nullable|date',
        'status'      => 'required|string|in:Belum Dikerjakan,Proses,Selesai',
        'link'        => 'nullable|url',
        'notes'       => 'nullable|string',
        'assigned_to_id' => 'required|exists:users,id',
    ]);

    $task->update($request->all());

    return redirect()->route('ad-hoc-tasks.index')
                     ->with('success', 'Tugas berhasil diperbarui');
}

public function upload(Request $request, $id)
{
    $task = AdHocTask::findOrFail($id);

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->storeAs('uploads', $filename, 'public');

        $task->file_path = 'uploads/'.$filename; // atau langsung 'adhoc_files/...' kalau mau seragam
        
    }
        if ($request->filled('link')) {
        $task->link = $request->input('link');
    }
$task->save();

    return redirect()->back()->with('success', 'File berhasil diupload.');
}


}