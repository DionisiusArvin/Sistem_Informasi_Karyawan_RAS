<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if (! Gate::allows('view-project')) {
            abort(403);
        }

        $statusFilter = $request->input('status', 'on-progress');
        $search       = $request->input('search');

        $projects = Project::with('tasks.dailyTasks');

        if (!empty($search)) {
            $projects->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('kode_proyek', 'like', "%{$search}%");
            });
        }

        $projects = $projects->get()->filter(function ($project) use ($statusFilter) {
            $progress = $project->getProgressPercentage();

            if ($statusFilter === 'on-progress') return $progress < 100;
            if ($statusFilter === 'finished') return $progress >= 100;

            return true;
        });

        if ($request->ajax()) {
            return view('projects.partials.grid', [
                'projects'     => $projects,
                'statusFilter' => $statusFilter,
            ])->render();
        }

        return view('projects.index', [
            'projects'     => $projects,
            'statusFilter' => $statusFilter,
            'search'       => $search,
        ]);
    }

    public function create()
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        $picUsers = User::whereIn('role', ['manager', 'kepala_divisi'])->get();
        return view('projects.create', compact('picUsers'));
    }

    public function store(Request $request)
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'kode_proyek'    => 'nullable|string|max:255|unique:projects,kode_proyek',
            'client_name'    => 'required|string|max:255',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'category'       => 'nullable|string|in:PBG,SLF,PBG dan SLF,PERENCANAAN,PENGAWASAN,KONSULTASI',
            'contract_value' => 'nullable|numeric|min:0',
            'pic_id'         => 'nullable|exists:users,id',
        ]);

        $validated['manager_id'] = Auth::id();

        $project = Project::create($validated);

        // ✅ AUTO BUAT CHECKLIST
        $this->createDefaultChecklist($project);

        // ✅ NOTIF KE PIC PROJECT
        NotificationService::send(
            $project->pic_id,
            'Anda Ditunjuk Sebagai PIC Project',
            'Project baru: ' . $project->name,
            route('projects.show', $project->id)
        );

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dibuat!');
    }

    public function edit(Project $project)
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        $users = User::whereIn('role', ['manager', 'kepala_divisi'])->get();

        return view('projects.edit', [
            'project' => $project,
            'users'   => $users
        ]);
    }

    public function update(Request $request, Project $project)
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'kode_proyek'    => ['nullable', 'string', 'max:255', Rule::unique('projects')->ignore($project->id)],
            'client_name'    => 'required|string|max:255',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'category'       => 'nullable|string|in:PBG,SLF,PBG dan SLF,PERENCANAAN,PENGAWASAN,KONSULTASI',
            'contract_value' => 'nullable|numeric|min:0',
            'pic_id'         => 'nullable|exists:users,id',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus!');
    }

    public function forceFinish(Project $project)
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        if (! $project->isForceFinished()) {
            $project->force_finished_at = now();
            $project->save();
        }

        return redirect()
            ->route('projects.show', $project->id)
            ->with('success', 'Proyek ditandai selesai secara paksa.');
    }

    public function show(Project $project)
    {
        if (! Gate::allows('view-project')) {
            abort(403);
        }

        $project->load('tasks.divisions', 'adminTasks.assignedToAdmin');

        $divisions = Division::all();

        return view('projects.show', [
            'project'   => $project,
            'divisions' => $divisions,
        ]);
    }

    private function createDefaultChecklist($project)
    {
        $gambarKerja = $project->checklists()->create(['name' => 'Gambar Kerja']);

        $gambarKerja->items()->createMany([
            ['name' => 'Denah'],
            ['name' => 'Tampak'],
            ['name' => 'Potongan'],
            ['name' => 'Detail Pondasi'],
        ]);

        $rab = $project->checklists()->create(['name' => 'Rencana Anggaran Biaya']);

        $rab->items()->createMany([['name' => 'Perhitungan Volume'],]);

        $project->checklists()->create(['name' => 'RKS']);
        $project->checklists()->create(['name' => 'Spesifikasi Teknis']);
    }
}
