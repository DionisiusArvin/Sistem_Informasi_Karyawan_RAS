<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\NotificationService;

class LeaveController extends Controller
{
    use AuthorizesRequests;

    /* ================= INDEX ================= */
    public function index(Request $request)
    {
        $user = Auth::user();

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
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->name . '%');
                });
            }

            $leaves = $query->latest()->get();
        } else {
            $leaves = Leave::with('user', 'division')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        $divisions = Division::all();

        return view('leaves.index', compact('leaves', 'divisions'));
    }

    /* ================= CREATE ================= */
    public function create()
    {
        $divisions = Division::all();
        return view('leaves.create', compact('divisions'));
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'start_date'  => 'required|date|after_or_equal:today',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'required|string',
            'type'        => 'required|in:sakit,izin',
        ];

        if ($user->role != 'admin') {
            $rules['division_id'] = 'required|exists:divisions,id';
        }

        $validated = $request->validate($rules);

        $leave = Leave::create([
            'user_id'     => $user->id,
            'division_id' => $validated['division_id'] ?? null,
            'start_date'  => $validated['start_date'],
            'end_date'    => $validated['end_date'],
            'reason'      => $validated['reason'],
            'type'        => $validated['type'],
            'status'      => 'pending',
        ]);

        // ================= NOTIF KE MANAGER =================
        $manager = User::where('role', 'manager')->first();

        if ($manager) {
            NotificationService::send(
                $manager->id,
                'Pengajuan Cuti Baru',
                $user->name . ' mengajukan cuti dari ' . $leave->start_date . ' s/d ' . $leave->end_date,
                route('leaves.index')
            );
        }

        return redirect()->route('leaves.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    /* ================= APPROVE ================= */
    public function approve(Leave $leave)
    {
        $this->authorize('approve-leave');

        $leave->update(['status' => 'approved']);

        // ================= NOTIF KE USER =================
        NotificationService::send(
            $leave->user_id,
            'Pengajuan Cuti Disetujui',
            'Pengajuan cuti Anda telah disetujui manager',
            route('leaves.index')
        );

        return back()->with('success', 'Cuti disetujui.');
    }

    /* ================= REJECT ================= */
    public function reject(Leave $leave)
    {
        $this->authorize('approve-leave');

        $leave->update(['status' => 'rejected']);

        // ================= NOTIF KE USER =================
        NotificationService::send(
            $leave->user_id,
            'Pengajuan Cuti Ditolak',
            'Pengajuan cuti Anda ditolak manager',
            route('leaves.index')
        );

        return back()->with('error', 'Cuti ditolak.');
    }
}
