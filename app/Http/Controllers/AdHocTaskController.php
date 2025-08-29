<?php

namespace App\Http\Controllers;

use App\Models\AdHocTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        $tasks = collect();

        if ($user->role === 'manager') {
            $tasks = AdHocTask::with(['assignedTo', 'assignedBy'])->latest()->get();
        } elseif ($user->role === 'kepala_divisi') {
            $staffIds = User::where('division_id', $user->division_id)->pluck('id');
            $tasks = AdHocTask::where('assigned_by_id', $user->id)
                ->orWhereIn('assigned_to_id', $staffIds)
                ->with(['assignedTo', 'assignedBy'])
                ->latest()->get();
        } else { // Ini akan berlaku untuk Staff & Admin
            $tasks = AdHocTask::where('assigned_to_id', $user->id)
                ->with(['assignedTo', 'assignedBy'])
                ->latest()->get();
        }

        return view('ad-hoc-tasks.index', ['tasks' => $tasks]);
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
        if ($adHocTask->assigned_to_id !== Auth::id()) {
            abort(403);
        }
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'notes' => 'nullable|string',
        ]);
        $filePath = $request->file('file')->store('adhoc_files', 'public');
        $adHocTask->update([
            'status' => 'Selesai',
            'file_path' => $filePath,
            'notes' => $validated['notes'],
        ]);
        return redirect()->route('ad-hoc-tasks.index')->with('success', 'Tugas mendadak berhasil di-upload.');
    }
}