<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Division;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /* ================= CREATE ================= */
    public function create(Project $project)
    {
        if (!Gate::allows('create-task')) {
            abort(403);
        }

        $divisions = Division::all();

        return view('tasks.create', compact('project', 'divisions'));
    }

    /* ================= STORE ================= */
    public function store(Request $request, Project $project)
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
            'PERENCANAAN' => [
                'Paving',
                'Rigid',
                'Taman',
                'Makam',
            ],
        ];
        $projectCategory = $project->category ?? null;
        $jenisTugasOptions = $jenisTugasOptionsByCategory[$projectCategory ?? ''] ?? null;
        $jenisTugasRule = $jenisTugasOptions
            ? 'required|string|in:' . implode(',', $jenisTugasOptions)
            : 'nullable|string|max:255';

        $isPbgSlf = in_array($projectCategory, ['PBG', 'SLF'], true);
        $pbgSlfMode = $request->input('pbg_slf_mode', 'auto');
        if ($isPbgSlf && $pbgSlfMode === 'auto') {
            $jenisTugasRule = 'nullable|string|max:255';
        }

        $isPavingPerencanaan = ($projectCategory ?? null) === 'PERENCANAAN'
            && ($request->input('jenis_tugas') ?? null) === 'Paving';
        $pavingMode = $request->input('paving_mode', 'auto');
        $nameRule = ($isPavingPerencanaan && $pavingMode === 'manual')
            ? 'required|string|max:255'
            : 'nullable|string|max:255';

        $validated = $request->validate([
            'jenis_tugas' => $jenisTugasRule,
            'name' => $nameRule,
            'description' => 'nullable|string',
            'divisions' => 'required|array',
            'divisions.*' => 'exists:divisions,id',
            'paving_mode' => 'nullable|in:auto,manual',
            'pbg_slf_mode' => 'nullable|in:auto,manual',
        ]);

        $autoTitle = trim((string) ($validated['name'] ?? ''));

        if ($isPbgSlf && $pbgSlfMode === 'auto') {
            $mainTasks = $jenisTugasOptionsByCategory[$projectCategory] ?? [];
            foreach ($mainTasks as $jenis) {
                $task = Task::create([
                    'project_id' => $project->id,
                    'jenis_tugas' => $jenis,
                    'name' => $autoTitle,
                    'description' => $validated['description'] ?? null,
                    'order' => 0,
                ]);
                $task->divisions()->sync($validated['divisions']);
            }
        } elseif (($projectCategory ?? null) === 'PERENCANAAN' && ($validated['jenis_tugas'] ?? null) === 'Paving' && $pavingMode === 'auto') {
            $pavingMainTasks = [
                'Survey',
                'Gambar Kerja',
                'Engineering Estimate',
                'BOQ',
                'Rencana Kerja dan Syarat2 Teknis',
                'Dokumen Teknis',
                'Harga Perkiraan Sendiri',
                'Laporan',
                'Finalisasi Dokumen Perencanaan',
            ];
            foreach ($pavingMainTasks as $taskName) {
                $finalName = $autoTitle !== '' ? ($taskName . ' ' . $autoTitle) : $taskName;
                $task = Task::create([
                    'project_id' => $project->id,
                    'jenis_tugas' => 'Paving',
                    'name' => $finalName,
                    'description' => $validated['description'] ?? null,
                    'order' => 0,
                ]);
                $task->divisions()->sync($validated['divisions']);
            }
        } else {
            $task = Task::create([
                'project_id' => $project->id,
                'jenis_tugas' => $validated['jenis_tugas'] ?? ($project->category ?? 'Non-PBG'),
                'name' => $validated['name'] ?? '',
                'description' => $validated['description'] ?? null,
                'order' => 0,
            ]);

            $task->divisions()->sync($validated['divisions']);
        }

        return redirect()
            ->route('projects.show', $project->id)
            ->with('success', 'Tugas berhasil dibuat.');
    }

    /* ================= SHOW (FIX TOTAL DI SINI) ================= */
    public function show(Task $task)
    {
        if (!Gate::allows('view-task', $task)) {
            abort(403);
        }

        $divisionIds = $task->divisions->pluck('id');

        $users = User::whereIn('role', ['staff', 'kepala_divisi'])
            ->whereIn('division_id', $divisionIds)
            ->get();

        // 🔥 INI KUNCI MASALAHMU SELAMA INI
        $task->load([
            'project.checklists.items',   // dropdown item pekerjaan
            'dailyTasks.assignedToStaff',
            'dailyTasks.activities',
        ]);

        return view('tasks.show', [
            'task'  => $task,
            'users' => $users,
        ]);
    }

    /* ================= DOWNLOAD ================= */
    public function download($id)
    {
        $upload = Upload::findOrFail($id);

        return response()->download(
            storage_path('app/public/' . $upload->file_path)
        );
    }

    /* ================= UPDATE DIVISION ================= */
    public function updateDivision(Request $request, Task $task)
    {
        if (!Gate::allows('update-task-division')) {
            abort(403);
        }

        $validated = $request->validate([
            'divisions'   => 'required|array',
            'divisions.*' => 'exists:divisions,id',
        ]);

        $task->divisions()->sync($validated['divisions']);

        return redirect()
            ->route('projects.show', $task->project_id)
            ->with('success', 'Divisi tugas berhasil diperbarui.');
    }

    /* ================= EDIT ================= */
    public function edit(Task $task)
    {
        if (!Gate::allows('view-task', $task)) {
            abort(403);
        }

        $divisions = Division::all();

        $task->load([
            'project.checklists.items',
            'dailyTasks.assignedToStaff',
            'dailyTasks.activities',
        ]);

        return view('tasks.edit', compact('task', 'divisions'));
    }

    /* ================= UPDATE ================= */
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
        'PERENCANAAN' => [
            'Paving',
            'Rigid',
            'Taman',
            'Makam',
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

        $task->update([
            'jenis_tugas' => $validated['jenis_tugas'] ?? $task->jenis_tugas,
            'name'        => $validated['name'] ?? '',
            'description' => $validated['description'] ?? null,
        ]);

        $task->divisions()->sync($validated['divisions']);

        return redirect()
            ->route('projects.show', $task->project_id)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /* ================= DELETE ================= */
    public function destroy(Task $task)
    {
        $task->divisions()->detach();
        $task->delete();

        return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
    }

    /* ================= REORDER ================= */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'order'            => 'required|array',
            'order.*.id'       => 'required|integer|exists:tasks,id',
            'order.*.order'    => 'required|integer',
        ]);

        foreach ($data['order'] as $item) {
            Task::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }

        return response()->json(['status' => 'ok']);
    }
}
