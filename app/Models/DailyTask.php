<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_id',
        'project_id',
        'name',
        'due_date',
        'status',
        'assigned_to_staff_id',
        'progress',
        'completion_status',
        'description',
    ];

    // RELASI: Satu tugas harian adalah bagian dari satu tugas utama
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    // RELASI: Satu tugas harian dikerjakan oleh satu user (staff)
    public function assignedToStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to_staff_id');
    }

    // RELASI: Satu tugas harian memiliki banyak aktivitas (komentar/upload)
    public function activities()
    {
        return $this->hasMany(TaskActivity::class, 'daily_task_id');
    }

    public function projects()
{
    return $this->belongsToMany(Project::class, 'daily_task_project', 'daily_task_id', 'project_id');
}


    public function getStatusBasedProgressAttribute(): int
    {
        return match ($this->status) {
            'Selesai' => 100,
            'Menunggu Validasi' => 75,
            'Lanjutkan' => 70,
            'Revisi' => 60,
            'Dikerjakan' => 50,
            'Belum Dikerjakan' => 25,
            default => 0, // 'Tersedia' atau status lain
        };
    }
    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($task) {
            if (empty($task->status)) {
                $task->status = 'Belum Diambil';
            }
        });
    }
}