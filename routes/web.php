<?php

use App\Http\Controllers\AdminTaskController;
use App\Http\Controllers\AdHocTaskController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DailyTaskController;
use App\Http\Controllers\DivisionTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class);
    Route::resource('projects.tasks', TaskController::class)->shallow();

    Route::post('/tasks/{task}/dailytasks', [DailyTaskController::class, 'store'])->name('tasks.dailytasks.store');
    Route::patch('/daily-tasks/{dailyTask}/claim', [DailyTaskController::class, 'claim'])->name('dailytasks.claim');
    Route::get('/daily-tasks/{dailyTask}/upload', [DailyTaskController::class, 'showUploadForm'])->name('dailytasks.upload.form');
    Route::post('/daily-tasks/{dailyTask}/upload', [DailyTaskController::class, 'handleUpload'])->name('dailytasks.upload.handle');
    Route::patch('/daily-tasks/{dailyTask}/approve', [DailyTaskController::class, 'approve'])->name('dailytasks.approve');
    Route::patch('/daily-tasks/{dailyTask}/reject', [DailyTaskController::class, 'reject'])->name('dailytasks.reject');
    Route::post('/daily-tasks/{dailyTask}/claim-and-upload', [DailyTaskController::class, 'claimAndUpload'])->name('dailytasks.claim_and_upload');
    Route::patch('/dailytasks/{dailyTask}', [DailyTaskController::class, 'update'])->name('dailytasks.update');
    Route::delete('/dailytasks/{dailyTask}', [DailyTaskController::class, 'destroy'])->name('dailytasks.destroy');

    Route::get('/division-tasks', [DivisionTaskController::class, 'index'])->name('division-tasks.index');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('admin-tasks', AdminTaskController::class);
    Route::get('/admin-tasks/{adminTask}/upload', [AdminTaskController::class, 'showUploadForm'])->name('admin-tasks.upload.form');
    Route::post('/admin-tasks/{adminTask}/upload', [AdminTaskController::class, 'handleUpload'])->name('admin-tasks.upload.handle');
    Route::get('/admin-tasks/{adminTask}/download', [AdminTaskController::class, 'downloadFile'])->name('admin-tasks.downloadFile');


    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/projects/export', [ReportController::class, 'exportProjects'])->name('reports.projects.export');
    Route::get('/reports/daily-tasks/export', [ReportController::class, 'exportDailyTasks'])->name('reports.daily-tasks.export');

    Route::resource('ad-hoc-tasks', AdHocTaskController::class);
    Route::get('/ad-hoc-tasks/{adHocTask}/upload', [AdHocTaskController::class, 'showUploadForm'])->name('ad-hoc-tasks.upload.form');
    Route::post('/ad-hoc-tasks/{adHocTask}/upload', [AdHocTaskController::class, 'handleUpload'])->name('ad-hoc-tasks.upload.handle');

    Route::get('/reports/daily-tasks/export', [ReportController::class, 'exportDailyTasks'])->name('reports.daily-tasks.export');

    Route::resource('leaves', LeaveController::class);
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');

    Route::patch('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::patch('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

});

require __DIR__.'/auth.php';
