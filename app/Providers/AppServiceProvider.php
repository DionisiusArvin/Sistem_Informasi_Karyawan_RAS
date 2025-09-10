<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Task;
use App\Models\DailyTask;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    protected $policies = [
        Task::class => TaskPolicy::class,
    ];
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-projects', function (User $user) {
            return $user->role === 'manager';
        });

        Gate::define('view-project', function (User $user) {
            return $user->role === 'manager' || $user->role === 'kepala_divisi' || $user->role === 'admin' || $user->role === 'staff';
        });

        Gate::define('create-task', function (User $user) {
            return $user->role === 'kepala_divisi';
        });

        // Modifikasi Gate ini
        Gate::define('view-task', function (User $user, Task $task) {
            // Izinkan jika user adalah Manager
            if ($user->role === 'manager') {
                return true;
            }
            // Izinkan jika user adalah Kepala Divisi DAN divisinya 
            // ada di dalam daftar kolaborator tugas tersebut.
            if ($user->role === 'kepala_divisi') {
                return $task->divisions->contains($user->division_id);
            }
            return false;
        });

        Gate::define('claim-task', function (User $user, DailyTask $dailyTask) {
            // ambil semua division_id yg terkait task
            $taskDivisionIds = $dailyTask->task->divisions->pluck('id')->toArray();

            // cek apakah divisi user ada di dalamnya
            $isCorrectDivision = in_array($user->division_id, $taskDivisionIds);

            return ($user->role === 'staff' || $user->role === 'kepala_divisi') && $isCorrectDivision;
        });


        Gate::define('validate-task', function (User $user, DailyTask $dailyTask) {
            // Manager bisa validasi semua
            if ($user->role === 'manager') {
                return true;
            }

            // Kepala Divisi hanya bisa validasi kalau divisinya ada di task terkait
            if ($user->role === 'kepala_divisi') {
                return $dailyTask->task->divisions()->where('divisions.id', $user->division_id)->exists();
            }

            return false;
        });

        Gate::define('update-task-division', function (User $user) {
            return $user->role === 'manager';
        });

        Gate::define('manage-admin-tasks', function (User $user){
            return $user->role === 'manager' || $user->role === 'admin';
        });

        Gate::define('view-reports', function (User $user) {
            return in_array($user->role, ['manager', 'kepala_divisi', 'staff']);
        });

        Gate::define('manage-ad-hoc-tasks', function (User $user) {
            return in_array($user->role, ['manager', 'kepala_divisi']);
        });

        Gate::define('approve-leave', function ($user) {
            return $user->role === 'manager';
        });

        Gate::define('reject-leave', function ($user) {
            return $user->role === 'manager';
        });

        Gate::define('view-leave', function ($user) {
            return $user->role === 'manager' || $user->role === 'kepala_divisi' || $user->role === 'admin' || $user->role === 'staff';
        });

        Gate::define('create-leave', function (User $user) {
            return $user->role === 'kepala_divisi' || $user->role === 'admin' || $user->role === 'staff';
        });

        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
    }
}
