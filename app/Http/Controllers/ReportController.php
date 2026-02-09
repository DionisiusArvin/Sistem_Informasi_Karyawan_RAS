<?php

namespace App\Http\Controllers;

use App\Models\DailyTask;
use App\Models\AdminTask;
use App\Exports\DailyTasksReportExport;
use App\Exports\AdminTasksReportExport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class ReportController extends Controller
{
    /* ======================================================
     * DAILY TASK REPORT (Staff / Kadiv / Manager)
     * ====================================================== */
    public function index(Request $request)
    {
        if (! Gate::allows('view-reports')) {
            abort(403);
        }

        $user = Auth::user();

        // ===== MODE FILTER =====
        $mode  = $request->input('mode', 'tanggal');
        $date  = $request->input('date');
        $from  = $request->input('from');
        $to    = $request->input('to');
        $month = $request->input('month');
        $year  = $request->input('year');

        $selectedUserId = $request->input('user_id');

        $reportDataQuery = DailyTask::with(['assignedToStaff', 'task.project']);

        // ===== FILTER TANGGAL FLEKSIBEL =====
        if ($mode === 'tanggal' && $date) {
            $reportDataQuery->whereDate('updated_at', Carbon::parse($date));
        }
        elseif ($mode === 'range' && $from && $to) {
            $reportDataQuery->whereBetween('updated_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }
        elseif ($mode === 'bulan' && $month && $year) {
            $reportDataQuery->whereMonth('updated_at', $month)
                            ->whereYear('updated_at', $year);
        }
        elseif ($mode === 'tahun' && $year) {
            $reportDataQuery->whereYear('updated_at', $year);
        }

        $filterableUsers = collect();

        // ===== ROLE LOGIC (AMAN) =====
        if ($user->role === 'manager') {
            $filterableUsers = User::whereIn('role', ['staff', 'kepala_divisi'])->get();

            if ($selectedUserId) {
                $reportDataQuery->where('assigned_to_staff_id', $selectedUserId);
            }
        }
        elseif ($user->role === 'kepala_divisi') {
            $staffIds = User::where('division_id', $user->division_id)->pluck('id');
            $filterableUsers = User::whereIn('id', $staffIds)->get();

            $reportDataQuery->whereHas('task.divisions', function ($query) use ($user) {
                $query->where('divisions.id', $user->division_id);
            });

            if ($selectedUserId) {
                $reportDataQuery->where('assigned_to_staff_id', $selectedUserId);
            }
        }
        elseif ($user->role === 'staff') {
            $reportDataQuery->where('assigned_to_staff_id', $user->id);
        }

        $reportData = $reportDataQuery->get();

        return view('reports.index', [
            'reportData'       => $reportData,
            'mode'             => $mode,
            'filterableUsers'  => $filterableUsers,
            'selectedUserId'   => $selectedUserId,
        ]);
    }


    /* ======================================================
     * EXPORT DAILY TASKS
     * ====================================================== */
    public function exportDailyTasks(Request $request)
    {
        $mode  = $request->input('mode', 'tanggal');
        $date  = $request->input('date');
        $from  = $request->input('from');
        $to    = $request->input('to');
        $month = $request->input('month');
        $year  = $request->input('year');

        return Excel::download(
            new DailyTasksReportExport($mode, $date, $from, $to, $month, $year),
            "daily_tasks_report.xlsx"
        );
    }


    /* ======================================================
     * ADMIN TASK REPORT
     * ====================================================== */
    public function adminTasks(Request $request)
    {
        if (! Gate::allows('manage-admin-tasks')) {
            abort(403);
        }

        $user = Auth::user();

        $mode  = $request->input('mode', 'tanggal');
        $date  = $request->input('date');
        $from  = $request->input('from');
        $to    = $request->input('to');
        $month = $request->input('month');
        $year  = $request->input('year');

        $selectedUserId = $request->input('user_id');

        $reportDataQuery = AdminTask::with(['assignedToAdmin', 'project']);

        if ($mode === 'tanggal' && $date) {
            $reportDataQuery->whereDate('updated_at', Carbon::parse($date));
        }
        elseif ($mode === 'range' && $from && $to) {
            $reportDataQuery->whereBetween('updated_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }
        elseif ($mode === 'bulan' && $month && $year) {
            $reportDataQuery->whereMonth('updated_at', $month)
                            ->whereYear('updated_at', $year);
        }
        elseif ($mode === 'tahun' && $year) {
            $reportDataQuery->whereYear('updated_at', $year);
        }

        $filterableUsers = collect();

        if ($user->role === 'manager') {
            $filterableUsers = User::where('role', 'admin')->get();

            if ($selectedUserId) {
                $reportDataQuery->where('assigned_to_admin_id', $selectedUserId);
            }
        }
        elseif ($user->role === 'admin') {
            $reportDataQuery->where('assigned_to_admin_id', $user->id);
        }

        $reportData = $reportDataQuery->get();

        return view('reports.admin', [
            'reportData'      => $reportData,
            'mode'            => $mode,
            'filterableUsers' => $filterableUsers,
            'selectedUserId'  => $selectedUserId,
        ]);
    }


    /* ======================================================
     * EXPORT ADMIN TASKS
     * ====================================================== */
    public function exportAdminTasks(Request $request)
    {
        if (! Gate::allows('manage-admin-tasks')) {
            abort(403);
        }

        $mode  = $request->input('mode', 'tanggal');
        $date  = $request->input('date');
        $from  = $request->input('from');
        $to    = $request->input('to');
        $month = $request->input('month');
        $year  = $request->input('year');
        $userId = $request->input('user_id');

        return Excel::download(
            new AdminTasksReportExport($mode, $date, $from, $to, $month, $year, $userId),
            "admin_tasks_report.xlsx"
        );
    }
}