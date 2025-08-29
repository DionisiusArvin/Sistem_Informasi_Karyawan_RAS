<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Division;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeaveController extends Controller
{
    use AuthorizesRequests;
    // List semua cuti (untuk manager lihat semua, untuk user hanya miliknya)
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            $leaves = Leave::with('user')->latest()->get();
        } else {
            $leaves = Leave::with('user')->where('user_id', $user->id)->latest()->get();
        }

        if ($user->role === 'manager') {
        $query = Leave::with('user', 'division');

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Filter divisi
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        // Filter nama user
        if ($request->filled('name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            });
        }

        $leaves = $query->latest()->get();
    } else {
        $leaves = Leave::with('user', 'division')
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }
        $divisions = Division::all(); // buat dropdown filter divisi
        return view('leaves.index', compact('leaves','divisions'));
        return view('leaves.index', compact('leaves'));
    }

    // Form create cuti
    public function create()
{
    $divisions = Division::all(); // ambil semua divisi
    return view('leaves.create', compact('divisions'));
}

    // Simpan cuti
   public function store(Request $request)
{
    $user = auth()->user();

    // rules dasar
    $rules = [
        'start_date'  => 'required|date|after_or_equal:today',
        'end_date'    => 'required|date|after_or_equal:start_date',
        'reason'      => 'required|string',
        'type'        => 'required|in:sakit,izin',
    ];

    // hanya staff/kepala_divisi yg wajib pilih division
    if ($user->role != 'admin') {
        $rules['division_id'] = 'required|exists:divisions,id';
    }

    $validated = $request->validate($rules);

    // buat data cuti
    $leave = Leave::create([
        'user_id'     => $user->id,
        'division_id' => $validated['division_id'] ?? null,
        'start_date'  => $validated['start_date'],
        'end_date'    => $validated['end_date'],
        'reason'      => $validated['reason'],
        'type'        => $validated['type'],
        'status'      => 'pending',
    ]);

    return redirect()->route('leaves.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
}


    // Approve cuti (khusus manager)
    public function approve(Leave $leave)
    {
        $this->authorize('approve-leave'); // pakai Gate
        $leave->update(['status' => 'approved']);
        return back()->with('success', 'Cuti disetujui.');
    }

    // Reject cuti (khusus manager)
    public function reject(Leave $leave)
    {
        $this->authorize('approve-leave'); // pakai Gate
        $leave->update(['status' => 'rejected']);
        return back()->with('error', 'Cuti ditolak.');
    }

    
}