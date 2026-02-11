<?php

namespace App\Http\Controllers;

use App\Models\AdHocTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\NotificationService;

class AdHocTaskController extends Controller
{
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

    public function create()
    {
        Gate::authorize('manage-ad-hoc-tasks');
        $users = User::where('role', '!=', 'manager')->get();
        return view('ad-hoc-tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-ad-hoc-tasks');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'weight' => 'required|integer|min:1|max:10', // ✅ tambah
        ]);

        $task = AdHocTask::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to_id' => $validated['assigned_to_id'],
            'due_date' => $validated['due_date'],
            'assigned_by_id' => Auth::id(),
            'status' => 'Belum Dikerjakan',
            'weight' => $validated['weight'], // ✅ tambah
        ]);

        NotificationService::send(
            $validated['assigned_to_id'],
            'Tugas Mendadak Untuk Anda',
            $validated['name'],
            route('ad-hoc-tasks.index') . '#adhoc-' . $task->id
        );

        return redirect()->route('ad-hoc-tasks.index')
            ->with('success', 'Tugas mendadak berhasil dibuat.');
    }

    public function edit(AdHocTask $adHocTask)
    {
        Gate::authorize('manage-ad-hoc-tasks');
        $users = User::where('role', '!=', 'manager')->get();
        return view('ad-hoc-tasks.edit', compact('adHocTask', 'users'));
    }

    public function update(Request $request, AdHocTask $adHocTask)
    {
        Gate::authorize('manage-ad-hoc-tasks');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Belum Dikerjakan,Menunggu Validasi,Proses,Selesai',
            'assigned_to_id' => 'required|exists:users,id',
            'weight' => 'required|integer|min:1|max:10', // ✅ tambah
        ]);

        $adHocTask->update($validated);

        NotificationService::send(
            $adHocTask->assigned_to_id,
            'Status Tugas Mendadak Diperbarui',
            'Tugas "' . $adHocTask->name . '" telah ' . $validated['status'],
            route('ad-hoc-tasks.index') . '#adhoc-' . $adHocTask->id
        );

        return redirect()->route('ad-hoc-tasks.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function uploadForm(AdHocTask $adHocTask)
    {
        if ($adHocTask->assigned_to_id !== Auth::id()) {
            abort(403);
        }

        return view('ad-hoc-tasks.upload', compact('adHocTask'));
    }

    public function showUploadForm(AdHocTask $adHocTask)
    {
        return $this->uploadForm($adHocTask);
    }

    public function handleUpload(Request $request, AdHocTask $adHocTask)
    {
        if ($adHocTask->assigned_to_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip,xls,xlsx|max:102400',
            'link_url' => 'nullable|url',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!$request->hasFile('file') && !$request->filled('link_url')) {
            return back()
                ->withErrors(['file' => 'Harus upload file atau isi link salah satu.'])
                ->withInput();
        }

        $filePath = $request->hasFile('file')
            ? $request->file('file')->store('ad_hoc_files', 'public')
            : null;

        $isKepalaDivisi = Auth::user()->role === 'kepala_divisi';

        $adHocTask->update([
            'file_path' => $filePath ?? $adHocTask->file_path,
            'link'      => $validated['link_url'] ?? $adHocTask->link,
            'notes'     => $validated['notes'] ?? $adHocTask->notes,
            'status'    => $isKepalaDivisi ? 'Selesai' : 'Menunggu Validasi',
        ]);

        $kepalaDivisi = User::where('role', 'kepala_divisi')
            ->where('division_id', Auth::user()->division_id)
            ->first();

        $manager = User::where('role', 'manager')->first();

        if ($kepalaDivisi) {
            NotificationService::send(
                $kepalaDivisi->id,
                'Tugas Mendadak Telah Dikerjakan',
                Auth::user()->name . ' mengupload hasil tugas: ' . $adHocTask->name,
                route('ad-hoc-tasks.index') . '#adhoc-' . $adHocTask->id
            );
        }

        if ($manager) {
            NotificationService::send(
                $manager->id,
                'Tugas Mendadak Telah Dikerjakan',
                Auth::user()->name . ' mengupload hasil tugas: ' . $adHocTask->name,
                route('ad-hoc-tasks.index') . '#adhoc-' . $adHocTask->id
            );
        }

        return redirect()->route('ad-hoc-tasks.index')
            ->with('success', $isKepalaDivisi
                ? 'Pekerjaan berhasil diupload & langsung selesai.'
                : 'Pekerjaan berhasil diupload, menunggu validasi.');
    }

    public function downloadFile(AdHocTask $adHocTask)
    {
        if (!$adHocTask->file_path) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download(
            storage_path('app/public/' . $adHocTask->file_path)
        );
    }

    public function destroy(AdHocTask $adHocTask)
    {
        Gate::authorize('manage-ad-hoc-tasks');

        $adHocTask->delete();

        return redirect()->route('ad-hoc-tasks.index')
            ->with('success', 'Tugas mendadak berhasil dihapus.');
    }
}
