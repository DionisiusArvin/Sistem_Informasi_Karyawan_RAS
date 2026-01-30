<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $schedules = Schedule::where('kepala_divisi', auth()->id())
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('date', 'asc')
            ->get();

        return view('schedules.index', compact('schedules', 'today'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'date' => 'required|date',
        ]);

        Schedule::create([
            'kepala_divisi' => auth()->id(),
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Jadwal berhasil dibuat.');
    }

    public function markDone($id)
    {
        $schedule = Schedule::findOrFail($id);

        $schedule->update([
            'status' => 'selesai'
        ]);

        return back();
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'name' => 'required',
            'date' => 'required',
        ]);

        $schedule->update($request->only('name','description','date'));

        return redirect()->route('schedules.index')->with('success', 'Jadwal diperbarui!');
    }
}
