<?php

namespace App\Http\Controllers;

use App\Models\DailyTask;
use App\Models\AdminTask;
use App\Models\AdHocTask;
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
     * DAILY + ADHOC TASK REPORT (Staff / Kadiv / Manager)
     * ====================================================== */
    public function index(Request $request)
    {
        if (! Gate::allows('view-reports')) {
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

        /* ================= DAILY TASK QUERY ================= */
        $reportDataQuery = DailyTask::with(['assignedToStaff', 'task.project']);

        // ===== FILTER TANGGAL DAILY =====
        if ($mode === 'tanggal' && $date) {
            $reportDataQuery->whereDate('updated_at', Carbon::parse($date));
        } elseif ($mode === 'range' && $from && $to) {
            $reportDataQuery->whereBetween('updated_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        } elseif ($mode === 'bulan' && $month && $year) {
            $reportDataQuery->whereMonth('updated_at', $month)
                            ->whereYear('updated_at', $year);
        } elseif ($mode === 'tahun' && $year) {
            $reportDataQuery->whereYear('updated_at', $year);
        }

        $filterableUsers = collect();

        // ===== ROLE FILTER DAILY =====
        if ($user->role === 'manager') {
            $filterableUsers = User::whereIn('role', ['staff', 'kepala_divisi'])->get();

            if ($selectedUserId) {
                $reportDataQuery->where('assigned_to_staff_id', $selectedUserId);
            }
        } elseif ($user->role === 'kepala_divisi') {
            $staffIds = User::where('division_id', $user->division_id)->pluck('id');
            $filterableUsers = User::whereIn('id', $staffIds)->get();

            $reportDataQuery->whereHas('task.divisions', function ($query) use ($user) {
                $query->where('divisions.id', $user->division_id);
            });

            if ($selectedUserId) {
                $reportDataQuery->where('assigned_to_staff_id', $selectedUserId);
            }
        } elseif ($user->role === 'staff') {
            $reportDataQuery->where('assigned_to_staff_id', $user->id);
        }

        $dailyTasks = $reportDataQuery->get();

        /* ================= ADHOC TASK QUERY ================= */
        $adHocTasks = AdHocTask::with(['assignedTo']);

        if ($mode === 'tanggal' && $date) {
            $adHocTasks->whereDate('created_at', Carbon::parse($date));
        } elseif ($mode === 'range' && $from && $to) {
            $adHocTasks->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        } elseif ($mode === 'bulan' && $month && $year) {
            $adHocTasks->whereMonth('created_at', $month)
                       ->whereYear('created_at', $year);
        } elseif ($mode === 'tahun' && $year) {
            $adHocTasks->whereYear('created_at', $year);
        }

        $adHocTasks = $adHocTasks->get();

        /* ================= GABUNG DAILY + ADHOC ================= */
        $reports = collect();

        foreach ($dailyTasks as $task) {
            $reports->push([
                'nama'    => $task->name,
                'pegawai' => $task->assignedToStaff->name ?? '-',
                'tipe'    => 'Tugas Harian',
                'tanggal' => $task->updated_at,
                'status'  => $task->status,
            ]);
        }

        foreach ($adHocTasks as $task) {
            $reports->push([
                'nama'    => $task->name,
                'pegawai' => $task->assignedTo->name ?? '-',
                'tipe'    => 'Tugas Mendadak',
                'tanggal' => $task->created_at,
                'status'  => $task->status,
            ]);
        }

        // 🔥 PENTING: TERBARU DI ATAS
        $reports = $reports->sortByDesc('tanggal')->values();

        return view('reports.index', [
            'reportData'      => $reports,
            'mode'            => $mode,
            'filterableUsers' => $filterableUsers,
            'selectedUserId'  => $selectedUserId,
        ]);
    }

    /* ======================================================
     * EXPORT DAILY TASKS
     * ====================================================== */
    public function exportDailyTasks(Request $request)
    {
        return Excel::download(
            new DailyTasksReportExport(
                $request->input('mode'),
                $request->input('date'),
                $request->input('from'),
                $request->input('to'),
                $request->input('month'),
                $request->input('year')
            ),
            "daily_tasks_report.xlsx"
        );
    }

    /* ======================================================
     * ADMIN TASK REPORT (TIDAK DIUBAH)
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
        } elseif ($mode === 'range' && $from && $to) {
            $reportDataQuery->whereBetween('updated_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        } elseif ($mode === 'bulan' && $month && $year) {
            $reportDataQuery->whereMonth('updated_at', $month)
                            ->whereYear('updated_at', $year);
        } elseif ($mode === 'tahun' && $year) {
            $reportDataQuery->whereYear('updated_at', $year);
        }

        if ($user->role === 'manager' && $selectedUserId) {
            $reportDataQuery->where('assigned_to_admin_id', $selectedUserId);
        } elseif ($user->role === 'admin') {
            $reportDataQuery->where('assigned_to_admin_id', $user->id);
        }

        $reportData = $reportDataQuery->latest('updated_at')->get();

        return view('reports.admin', [
            'reportData' => $reportData,
            'mode'       => $mode,
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

        return Excel::download(
            new AdminTasksReportExport(
                $request->input('mode'),
                $request->input('date'),
                $request->input('from'),
                $request->input('to'),
                $request->input('month'),
                $request->input('year'),
                $request->input('user_id')
            ),
            "admin_tasks_report.xlsx"
        );
    }
}
