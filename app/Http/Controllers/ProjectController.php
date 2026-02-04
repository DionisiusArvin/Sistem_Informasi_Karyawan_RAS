<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (! Gate::allows('view-project')) {
            abort(403);
        }
    
        $statusFilter = $request->input('status', 'on-progress');
        $search       = $request->input('search');
    
        $projects = Project::with('tasks.dailyTasks');
    
        if (!empty($search)) {
            $projects->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('kode_proyek', 'like', "%{$search}%");
            });
        }
    
        $projects = $projects->get()->filter(function ($project) use ($statusFilter) {
            $progress = $project->getProgressPercentage();
    
            if ($statusFilter === 'on-progress') {
                return $progress < 100;
            }
            if ($statusFilter === 'finished') {
                return $progress >= 100;
            }
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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pastikan hanya manager yang bisa membuat proyek
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }
        $picUsers = User::whereIn('role', ['manager', 'kepala_divisi'])->get();
        return view('projects.create', compact('picUsers'));
    }

    public function store(Request $request)
    {
        // 1. Pastikan hanya manager yang bisa menyimpan proyek
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        // 2. Validasi semua input dari form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'kode_proyek' => 'nullable|string|max:255|unique:projects,kode_proyek', // <-- Tambahkan
            'client_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|string|in:PBG,SLF,PBG dan SLF,PERENCANAAN,PENGAWASAN,KONSULTASI',
            'contract_value' => 'nullable|numeric|min:0',
            'pic_id' => 'nullable|exists:users,id',
        ]);

        // 3. Tambahkan ID manager yang sedang login ke data
        $validated['manager_id'] = Auth::id();

        // 4. Buat dan simpan proyek baru
        Project::create($validated);

        // 5. Arahkan kembali ke halaman daftar proyek dengan pesan sukses
        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dibuat!');
    }
    
    public function edit(Project $project) // Laravel akan otomatis mencari project berdasarkan ID
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }
        $users = User::whereIn('role', ['manager', 'kepala_divisi'])->get();

        return view('projects.edit', [
            'project' => $project,
            'users' => $users
        ]);
    }

    public function update(Request $request, Project $project)
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        // Validasi data yang diubah
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'kode_proyek' => [
            'nullable',
            'string',
            'max:255',
            Rule::unique('projects')->ignore($project->id),
        ],
            'client_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|string|in:PBG,SLF,PBG dan SLF,PERENCANAAN,PENGAWASAN,KONSULTASI',
            'contract_value' => 'nullable|numeric|min:0',
            'pic_id' => 'nullable|exists:users,id',
        ]);

        // Update data proyek di database
        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil diperbarui!');
    }
    
    public function destroy(Project $project)
    {
        // Pastikan hanya manager yang bisa menghapus
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        // Hapus proyek dari database
        $project->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus!');
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

        // Muat relasi task + divisions + relasi adminTask
        $project->load('tasks.divisions', 'adminTasks.assignedToAdmin'); 

        // Ambil semua divisi (untuk pilihan manager ubah akses)
        $divisions = Division::all();

        $breadcrumbs = [
            ['label' => 'Proyek', 'url' => route('projects.index')],
            ['label' => $project->name, 'url' => route('projects.show', $project->id)],
        ];

        return view('projects.show', [
            'project'   => $project,
            'divisions' => $divisions, 
        ]);
    }
}
