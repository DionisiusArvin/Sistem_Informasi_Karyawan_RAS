<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use App\Models\Project;
use App\Models\Task;
use App\Models\AdminTask;
use App\Models\AdHocTask;
use App\Models\DailyTask;
use App\Models\Division;

// === DASHBOARD ===
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

// === PROYEK ===
Breadcrumbs::for('projects.index', function (BreadcrumbTrail $trail) {
    $trail->push('Proyek', route('projects.index'));
});

Breadcrumbs::for('projects.show', function (BreadcrumbTrail $trail, ?Project $project = null) {
    $trail->parent('projects.index');
    $name = $project->name ?? 'Detail Proyek';
    $trail->push($name, $project ? route('projects.show', $project) : '#');
});

Breadcrumbs::for('projects.create', function (BreadcrumbTrail $trail) {
    $trail->parent('projects.index');
    $trail->push('Buat Proyek', route('projects.create'));
});

Breadcrumbs::for('projects.edit', function (BreadcrumbTrail $trail, ?Project $project = null) {
    $trail->parent('projects.show', $project);
    $trail->push('Edit Proyek', $project ? route('projects.edit', $project) : '#');
});


// === TUGAS DALAM PROYEK ===

// Buat tugas
Breadcrumbs::for('projects.tasks.create', function (BreadcrumbTrail $trail, Project $project) {
    $trail->parent('projects.show', $project);
    $trail->push('Buat Tugas', route('projects.tasks.create', $project));
});

// Detail tugas
Breadcrumbs::for('projects.tasks.show', function (BreadcrumbTrail $trail, Project $project, Task $task) {
    $trail->parent('projects.show', $project, );
    $trail->push($task->name ?? 'Detail Tugas', route('projects.tasks.show', [$project, $task]));
});

Breadcrumbs::for('tasks.show', function (BreadcrumbTrail $trail, Task $task) {
    $trail->parent('projects.show', $task->project);
    $trail->push($task->name ?? 'Detail Tugas', route('tasks.show', [$task->project, $task]));
});

Breadcrumbs::for('tasks.dailytasks.upload.form', function (BreadcrumbTrail $trail,  Task $task, DailyTask $dailyTask) {
    $trail->parent('tasks.show', $dailyTask->task);
    $trail->push($dailyTask->name ?? 'Upload Tugas Harian', route('tasks.dailytasks.upload.form', [$dailyTask, $task]));
});

Breadcrumbs::for('dailytasks.upload.form', function (BreadcrumbTrail $trail, DailyTask $dailytask) {
    $trail->parent('division-tasks.index');
    $trail->push($dailytask->name ?? 'Detail Tugas Harian', route('dailytasks.upload.form', $dailytask));
});

// Edit tugas
Breadcrumbs::for('projects.tasks.edit', function (BreadcrumbTrail $trail, Project $project, Task $task) {
    $trail->parent('projects.tasks.show', $project, $task);
    $trail->push('Edit Tugas', route('projects.tasks.edit', [$project, $task]));
});

Breadcrumbs::for('tasks.edit', function (BreadcrumbTrail $trail, Task $task) {
    $trail->parent('projects.show', $task->project);
    $trail->push($task->name ?? 'Detail Tugas', route('tasks.edit', [$task->project, $task]));
});

Breadcrumbs::for('validation.index', function (BreadcrumbTrail $trail) {
    $trail->push('Validasi', route('validation.index'));
});

Breadcrumbs::for('division-tasks.index', function (BreadcrumbTrail $trail) {
    $trail->push('Tugasmu', route('division-tasks.index'));
});

Breadcrumbs::for('admin-tasks.index', function (BreadcrumbTrail $trail) {
    $trail->push('Tugas Admin', route('admin-tasks.index'));
});

Breadcrumbs::for('admin-tasks.edit', function (BreadcrumbTrail $trail, ?AdminTask $admintask = null) {
    $trail->parent('admin-tasks.index', $admintask);
    $trail->push('Edit Proyek', $admintask ? route('admin-tasks.edit', $admintask) : '#');
});

Breadcrumbs::for('admin-tasks.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin-tasks.index');
    $trail->push('Edit Tugas', route('admin-tasks.create'));
});

Breadcrumbs::for('admin-tasks.upload.form', function (BreadcrumbTrail $trail, AdminTask $adminTask) {
    // naik ke detail proyek (karena upload admin task ada di halaman project)
    if ($adminTask->project) {
        $trail->parent('projects.show', $adminTask->project);
    } else {
        $trail->parent('admin-tasks.index');
    }

    $trail->push(
        'Upload Tugas Administrasi',
        route('admin-tasks.upload.form', $adminTask)
    );
});

Breadcrumbs::for('reports.index', function (BreadcrumbTrail $trail) {
    $trail->push('Laporan', route('reports.index'));
});

Breadcrumbs::for('reports.admin-tasks', function (BreadcrumbTrail $trail) {
    $trail->push('Laporan Admin', route('reports.admin-tasks'));
});

Breadcrumbs::for('ad-hoc-tasks.index', function (BreadcrumbTrail $trail) {
    $trail->push('Tugas Mendadak', route('ad-hoc-tasks.index'));
});

Breadcrumbs::for('ad-hoc-tasks.edit', function (BreadcrumbTrail $trail, $adhoctask = null) {
    $trail->parent('ad-hoc-tasks.index');

    if ($adhoctask instanceof AdHocTask) {
        $trail->push('Edit Tugas: ' . $adhoctask->name, route('ad-hoc-tasks.edit', $adhoctask));
    } else {
        $trail->push('Edit Tugas', route('ad-hoc-tasks.edit', $adhoctask ?? '#'));
    }
});

Breadcrumbs::for('ad-hoc-tasks.create', function (BreadcrumbTrail $trail) {
    $trail->parent('ad-hoc-tasks.index');
    $trail->push('Buat Tugas mendadak', route('ad-hoc-tasks.create'));
});

Breadcrumbs::for('ad-hoc-tasks.upload.form', function (BreadcrumbTrail $trail, AdHocTask $adhoctask) {
    $trail->parent('ad-hoc-tasks.index');
    $trail->push('Upload', route('ad-hoc-tasks.upload.form', $adhoctask));
});

Breadcrumbs::for('leaves.index', function (BreadcrumbTrail $trail) {
    $trail->push('Mau Ambil Cuti yaaaaa', route('leaves.index'));
});

Breadcrumbs::for('leaves.create', function (BreadcrumbTrail $trail) {
    $trail->parent('leaves.index');
    $trail->push('Pengajuan Cuti', route('leaves.create'));
});

Breadcrumbs::for('profile.edit', function (BreadcrumbTrail $trail) {
    $trail->push('Profile', route('profile.edit'));
});

Breadcrumbs::for('schedules.index', function (BreadcrumbTrail $trail) {
    $trail->push('Jadwal', route('schedules.index'));
});

// === PERFORMA KARYAWAN ===
Breadcrumbs::for('performance.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Performa Karyawan', route('performance.index'));
});

Breadcrumbs::for('performance.calculate', function (BreadcrumbTrail $trail) {
    $trail->parent('performance.index');
    $trail->push('Hasil Perhitungan');
});

Breadcrumbs::for('performance.pdf', function (BreadcrumbTrail $trail) {
    $trail->parent('performance.index');
    $trail->push('Export PDF');
});

Breadcrumbs::for('performance.excel', function (BreadcrumbTrail $trail) {
    $trail->parent('performance.index');
    $trail->push('Export Excel');
});
