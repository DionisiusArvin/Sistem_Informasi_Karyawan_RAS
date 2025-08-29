<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;
    protected $guarded = [];

    // RELASI: Satu project dimiliki oleh satu user (manager)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // RELASI: Satu project memiliki banyak tugas utama
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function dailyTasks()
    {
        return $this->hasManyThrough(DailyTask::class, Task::class);
    }

    public function adminTasks()
    {
        return $this->hasMany(AdminTask::class);
    }

    public function getProgressPercentage()
    {
        // Eager load relasi untuk efisiensi
        $tasks = $this->tasks()->with('dailyTasks')->get();

        if ($tasks->isEmpty()) {
            return 0; // Jika tidak ada tugas utama, progress 0%
        }

        // Menghitung rata-rata dari progress setiap tugas utama
        $averageProgress = $tasks->avg(function ($task) {
            return $task->getProgressPercentage();
        });

        return round($averageProgress);
    }


    public function getHealthStatus(): string
    {
        Log::info('💥 Masuk getHealthStatus()');

        $workProgress = $this->getProgressPercentage();
        $today = \Carbon\Carbon::today('Asia/Jakarta');

        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate = \Carbon\Carbon::parse($this->end_date);

        $totalDuration = $startDate->diffInDays($endDate) + 1;
        if ($totalDuration <= 1) {
            return $workProgress == 100 ? 'aman' : 'bahaya';
        }

        $daysPassed = $startDate->diffInDays($today) + 1;

        if ($today->isBefore($startDate)) {
            $timeProgress = 0;
        } elseif ($today->isAfter($endDate)) {
            $timeProgress = 100;
        } else {
            $timeProgress = round(($daysPassed / $totalDuration) * 100);
        }

        $deviation = $workProgress - $timeProgress;

        Log::info("✅ Progress: $workProgress, Time: $timeProgress, Dev: $deviation");

        // ✅ Tambahan logika khusus:
        if ($workProgress == 0 && $timeProgress > 0) {
            return 'bahaya'; // Tidak ada progress padahal waktu sudah berjalan
        }

        if ($deviation >= -5) {
            return 'aman';
        } elseif ($deviation < -5 && $deviation > -15) {
            return 'perhatian';
        } else {
            return 'bahaya';
        }
    }



}
