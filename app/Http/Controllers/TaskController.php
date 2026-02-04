<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task; // Impor model Task
use App\Models\User; // Impor model User
use App\Models\Division;
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

    $divisions = Division::all();

    return view('tasks.create', compact('project', 'divisions'));
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        if (! Gate::allows('create-task')){
            abort(403);
        }
        $jenisTugasOptionsByCategory = [
            'PBG' => [
                'Data Umum',
                'Data Teknis Arsitektur',
                'Data Teknis Struktur',
                'Data Teknis MEP',
                'Data Tambahan',
                'Upload',
            ],
            'SLF' => [
                'Data Umum',
                'Data Teknis Arsitektur',
                'Data Teknis Struktur',
                'Data Teknis MEP',
                'Upload',
            ],
        ];
        $jenisTugasOptions = $jenisTugasOptionsByCategory[$project->category ?? ''] ?? null;
        $jenisTugasRule = $jenisTugasOptions
            ? 'required|string|in:' . implode(',', $jenisTugasOptions)
            : 'nullable|string|max:255';

        $validated = $request->validate([
            'jenis_tugas' => $jenisTugasRule,
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'divisions' => 'required|array', // Validasi untuk array divisi
            'divisions.*' => 'exists:divisions,id',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'jenis_tugas' => $validated['jenis_tugas'] ?? ($project->category ?? 'Non-PBG'),
            'name' => $validated['name'] ?? '',
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
            'users' => $staffInDivision
        ]);
    }

    public function download($id)
{
    $upload = Upload::findOrFail($id);
    return response()->download(storage_path('app/public/' . $upload->file_path));
}

public function updateDivision(Request $request, Task $task)
{
    if (! Gate::allows('update-task-division')) {
        abort(403);
    }

    $validated = $request->validate([
        'divisions'   => 'required|array',
        'divisions.*' => 'exists:divisions,id',
    ]);

    // sinkronkan divisi (hapus yang lama, ganti dengan yang baru)
    $task->divisions()->sync($validated['divisions']);

    return redirect()->route('projects.show', $task->project_id)
                     ->with('success', 'Divisi tugas berhasil diperbarui.');
}

public function edit(Task $task)
{
    if (! Gate::allows('view-task', $task)) {
            abort(403);
        }

        // Ambil semua ID divisi yang berkolaborasi dalam tugas ini
        $divisionIds = $task->divisions->pluck('id');

        $divisions = Division::all();

        // sinkronkan divisi (hapus yang lama, ganti dengan yang baru)

        // Ambil semua staff yang berada di dalam divisi-divisi tersebut
        $staffInDivision = User::where('role', 'staff')
                                ->whereIn('division_id', $divisionIds)
                                ->get();
        
        // Muat relasi yang dibutuhkan untuk tampilan
        $task->load('dailyTasks.assignedToStaff', 'dailyTasks.activities'); 

    return view('tasks.edit', compact('task', 'divisions'));
}
public function update(Request $request, Task $task)
{
    $jenisTugasOptionsByCategory = [
        'PBG' => [
            'Data Umum',
            'Data Teknis Arsitektur',
            'Data Teknis Struktur',
            'Data Teknis MEP',
            'Data Tambahan',
            'Upload',
        ],
        'SLF' => [
            'Data Umum',
            'Data Teknis Arsitektur',
            'Data Teknis Struktur',
            'Data Teknis MEP',
            'Upload',
        ],
    ];
    $jenisTugasOptions = $jenisTugasOptionsByCategory[$task->project->category ?? ''] ?? null;
    $jenisTugasRule = $jenisTugasOptions
        ? 'required|string|in:' . implode(',', $jenisTugasOptions)
        : 'nullable|string|max:255';

    $validated = $request->validate([
        'jenis_tugas' => $jenisTugasRule,
        'name'        => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'divisions'   => 'required|array',
        'divisions.*' => 'exists:divisions,id',
    ]);

    // Update hanya kolom yang ada di tabel tasks
    $task->update([
        'jenis_tugas' => $validated['jenis_tugas'] ?? $task->jenis_tugas,
        'name'        => $validated['name'] ?? '',
        'description' => $validated['description'] ?? null,
    ]);

    // Sinkronkan relasi divisi
    $task->divisions()->sync($validated['divisions']);

    return redirect()
        ->route('projects.show', $task->project_id)
        ->with('success', 'Tugas berhasil diperbarui.');
}
public function destroy(Task $task)
{
    // hapus relasi dengan divisions (jika pakai many-to-many)
    $task->divisions()->detach();

    // hapus task
    $task->delete();

    return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
}


public function reorder(Request $request)
{
    $data = $request->validate([
        'order' => 'required|array',
        'order.*.id' => 'required|integer|exists:tasks,id',
        'order.*.order' => 'required|integer',
    ]);

    foreach ($data['order'] as $item) {
        \App\Models\Task::where('id', $item['id'])->update(['order' => $item['order']]);
    }

    // kembalikan success
    return response()->json(['status' => 'ok']);
}

}
