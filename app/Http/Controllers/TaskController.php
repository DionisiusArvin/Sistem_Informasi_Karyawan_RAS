<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task; // Impor model Task
use App\Models\User; // Impor model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Impor Auth
use Illuminate\Support\Facades\Gate; // Impor Gate

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        if (! Gate::allows('create-task')) {
            abort(403);
        }
        // Tampilkan form untuk membuat tugas baru
        return view('tasks.create', ['project' => $project]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        if (! Gate::allows('create-task')){
            abort(403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'divisions' => 'required|array', // Validasi untuk array divisi
            'divisions.*' => 'exists:divisions,id',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        // Lampirkan semua divisi yang dipilih ke tugas utama
        $task->divisions()->attach($validated['divisions']);

        return redirect()->route('projects.show', $project->id)->with('success', 'Tugas baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if (! Gate::allows('view-task', $task)) {
            abort(403);
        }

        // Ambil semua ID divisi yang berkolaborasi dalam tugas ini
        $divisionIds = $task->divisions->pluck('id');

        // Ambil semua staff yang berada di dalam divisi-divisi tersebut
        $staffInDivision = User::where('role', 'staff')
                                ->whereIn('division_id', $divisionIds)
                                ->get();
        
        // Muat relasi yang dibutuhkan untuk tampilan
        $task->load('dailyTasks.assignedToStaff', 'dailyTasks.activities');

        return view('tasks.show', [
            'task' => $task,
            'staff' => $staffInDivision
        ]);
    }

}
