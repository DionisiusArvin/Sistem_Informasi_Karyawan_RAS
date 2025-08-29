<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $guarded = [];

    // RELASI: Satu tugas utama adalah bagian dari satu project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    //RELASI: Satu tugas utama dapat memiliki banyak divisi yang berkolaborasi
    public function divisions()
    {
        return $this->belongsToMany(Division::class, 'division_task_pivot', 'task_id', 'division_id');
    }


    // RELASI: Satu tugas utama memiliki banyak tugas harian
    public function dailyTasks()
    {
        return $this->hasMany(DailyTask::class);
    }

    // Method baru untuk menghitung progress
    public function getProgressPercentage()
    {
        $totalDailyTasks = $this->dailyTasks()->count();
        if ($totalDailyTasks == 0) {
            return 0; // Jika tidak ada tugas harian, progress 0%
        }
        $completedDailyTasks = $this->dailyTasks()->where('status', 'Selesai')->count();
        return round(($completedDailyTasks / $totalDailyTasks) * 100);
    }
}