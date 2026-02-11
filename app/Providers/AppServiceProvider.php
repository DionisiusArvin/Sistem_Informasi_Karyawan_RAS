<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Task;
use App\Models\DailyTask;
use App\Policies\TaskPolicy;
use App\Observers\TaskObserver;

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
        /* ======================================================
         * REGISTER OBSERVER
         * ====================================================== */
        Task::observe(TaskObserver::class);

        /* ======================================================
         * GATES
         * ====================================================== */
        Gate::define('manage-projects', fn (User $user) => $user->role === 'manager');

        Gate::define('view-project', fn (User $user) =>
            in_array($user->role, ['manager', 'kepala_divisi', 'admin', 'staff'])
        );

        Gate::define('create-task', fn (User $user) => $user->role === 'kepala_divisi');

        Gate::define('view-task', function (User $user, Task $task) {
            if ($user->role === 'manager') return true;

            if (in_array($user->role, ['kepala_divisi', 'staff'])) {
                return $task->divisions->contains($user->division_id);
            }

            return false;
        });

        Gate::define('claim-task', function (User $user, DailyTask $dailyTask) {
            $taskDivisionIds = $dailyTask->task->divisions->pluck('id')->toArray();

            return in_array($user->role, ['staff', 'kepala_divisi']) &&
                   in_array($user->division_id, $taskDivisionIds);
        });

        Gate::define('validate-task', function (User $user, DailyTask $dailyTask) {
            if ($user->role === 'manager') return true;

            if ($user->role === 'kepala_divisi') {
                return $dailyTask->task
                    ->divisions()
                    ->where('divisions.id', $user->division_id)
                    ->exists();
            }

            return false;
        });

        Gate::define('update-task-division', fn (User $user) =>
            in_array($user->role, ['manager', 'kepala_divisi'])
        );

        Gate::define('manage-admin-tasks', fn (User $user) =>
            in_array($user->role, ['manager', 'admin'])
        );

        Gate::define('view-reports', fn (User $user) =>
            in_array($user->role, ['manager', 'kepala_divisi', 'staff'])
        );

        Gate::define('manage-ad-hoc-tasks', fn (User $user) =>
            in_array($user->role, ['manager', 'kepala_divisi'])
        );

        Gate::define('approve-leave', fn (User $user) => $user->role === 'manager');
        Gate::define('reject-leave', fn (User $user) => $user->role === 'manager');

        Gate::define('view-leave', fn (User $user) =>
            in_array($user->role, ['manager', 'kepala_divisi', 'admin', 'staff'])
        );

        Gate::define('create-leave', fn (User $user) =>
            in_array($user->role, ['kepala_divisi', 'admin', 'staff'])
        );

        /* ======================================================
         * LOCALE & TIMEZONE
         * ====================================================== */
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
    }
}
