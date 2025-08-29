<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // Pastikan Gate diimpor
use Illuminate\Support\Facades\Auth;

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

        $statusFilter = $request->input('status', 'on-progress'); // Default ke 'on-progress'

        $allProjects = Project::with('tasks.dailyTasks')->latest()->get();

        $projects = $allProjects->filter(function ($project) use ($statusFilter) {
            $progress = $project->getProgressPercentage();
            
            if ($statusFilter === 'on-progress') {
                return $progress < 100;
            }
            if ($statusFilter === 'finished') {
                return $progress >= 100;
            }
            return true; // Untuk 'semua'
        });

        return view('projects.index', [
            'projects' => $projects,
            'statusFilter' => $statusFilter // Kirim status filter ke view
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
        
        return view('projects.create');
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

        return view('projects.edit', ['project' => $project]);
    }

    public function update(Request $request, Project $project)
    {
        if (! Gate::allows('manage-projects')) {
            abort(403);
        }

        // Validasi data yang diubah
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'kode_proyek' => 'nullable|string|max:255|unique:projects,kode_proyek', // <-- Tambahkan
            'client_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
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

    public function show(Project $project)
    {
        if (! Gate::allows('view-project')) {
            abort(403);
        }
        
        // PERBAIKI DI SINI: Muat relasi 'divisions' yang baru
        $project->load('tasks.divisions', 'adminTasks.assignedToAdmin'); 

        return view('projects.show', ['project' => $project]);
    }
    // ... method lainnya akan kita isi nanti
}