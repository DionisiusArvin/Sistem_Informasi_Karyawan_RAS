<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    AdminTaskController,
    AdHocTaskController,
    ReportController,
    ProfileController,
    ProjectController,
    TaskController,
    DailyTaskController,
    DivisionTaskController,
    DashboardController,
    LeaveController,
    ValidationController,
    ScheduleController,
    PerformanceController
};

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // VALIDATION
    Route::prefix('validation')->group(function () {
        Route::get('/', [ValidationController::class, 'index'])->name('validation.index');
        Route::post('{id}/approve', [ValidationController::class, 'approve'])->name('validation.approve');
        Route::post('{id}/continue', [ValidationController::class, 'continue'])->name('validation.continue');
        Route::post('{id}/reject', [ValidationController::class, 'reject'])->name('validation.reject');
    });

    // PROJECTS & TASKS
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.tasks', TaskController::class)->shallow();

    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::patch('/tasks/{task}/update-division', [TaskController::class, 'updateDivision'])->name('tasks.updateDivision');
    Route::patch('/tasks/{task}/update-info', [TaskController::class, 'updateInfo'])->name('tasks.updateInfo');

    // DAILY TASKS
    Route::post('/tasks/{task}/dailytasks', [DailyTaskController::class, 'store'])
        ->name('tasks.dailytasks.store');

    Route::post('/daily-tasks/{dailyTask}/claim-and-upload', [DailyTaskController::class, 'claimAndUpload'])
        ->name('dailytasks.claim_and_upload');

    Route::get('/daily-tasks/{dailyTask}/download', [DailyTaskController::class, 'download'])
        ->name('dailytasks.download');

    Route::prefix('daily-tasks')->group(function () {
        Route::patch('{dailyTask}/claim', [DailyTaskController::class, 'claim'])->name('dailytasks.claim');

        Route::get('{dailyTask}/upload', [DailyTaskController::class, 'showUploadForm'])->name('dailytasks.upload.form');
        Route::post('{dailyTask}/upload', [DailyTaskController::class, 'handleUpload'])->name('dailytasks.upload.handle');

        Route::patch('{dailyTask}/approve', [DailyTaskController::class, 'approve'])->name('dailytasks.approve');
        Route::patch('{dailyTask}/reject', [DailyTaskController::class, 'reject'])->name('dailytasks.reject');

        Route::patch('{dailyTask}', [DailyTaskController::class, 'update'])->name('dailytasks.update');
        Route::delete('{dailyTask}', [DailyTaskController::class, 'destroy'])->name('dailytasks.destroy');
    });

    // DIVISION TASKS
    Route::get('/division-tasks', [DivisionTaskController::class, 'index'])
        ->name('division-tasks.index');

    // ADMIN TASKS
    Route::resource('admin-tasks', AdminTaskController::class);
    Route::get('/admin-tasks/{adminTask}/upload',
        [AdminTaskController::class, 'showUploadForm']
    )->name('admin-tasks.upload.form');
    Route::post('/admin-tasks/{adminTask}/upload',
        [AdminTaskController::class, 'handleUpload']
    )->name('admin-tasks.upload.handle');
    Route::get('/admin-tasks/{adminTask}/download',
        [AdminTaskController::class, 'downloadFile']
    )->name('admin-tasks.downloadFile');

    // AD HOC TASKS
    Route::resource('ad-hoc-tasks', AdHocTaskController::class);
    Route::get('/ad-hoc-tasks/{adHocTask}/upload', [AdHocTaskController::class, 'showUploadForm'])->name('ad-hoc-tasks.upload.form');
    Route::post('/ad-hoc-tasks/{adHocTask}/upload', [AdHocTaskController::class, 'handleUpload'])->name('ad-hoc-tasks.upload.handle');
    Route::get('/ad-hoc-tasks/{adHocTask}/download', [AdHocTaskController::class, 'downloadFile'])->name('ad-hoc-tasks.downloadFile');

    // LEAVES
    Route::resource('leaves', LeaveController::class);
    Route::patch('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::patch('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

    // REPORTS
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/projects/export', [ReportController::class, 'exportProjects'])->name('reports.projects.export');
    Route::get('/reports/daily-tasks/export', [ReportController::class, 'exportDailyTasks'])->name('reports.daily-tasks.export');

    // SCHEDULES
    Route::resource('schedules', ScheduleController::class);
    Route::post('/schedules/{schedule}/done', [ScheduleController::class, 'markDone'])
        ->name('schedules.done');
});

/*
|--------------------------------------------------------------------------
| PERFORMANCE KPI (MANAGER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:manager'])
    ->prefix('performance')
    ->group(function () {
        Route::get('/', [PerformanceController::class, 'index'])->name('performance.index');
        Route::post('/calculate', [PerformanceController::class, 'calculate'])->name('performance.calculate');
        Route::post('/pdf', [PerformanceController::class, 'exportPdf'])->name('performance.pdf');
        Route::post('/excel', [PerformanceController::class, 'exportExcel'])->name('performance.excel');
    });

require __DIR__.'/auth.php';
