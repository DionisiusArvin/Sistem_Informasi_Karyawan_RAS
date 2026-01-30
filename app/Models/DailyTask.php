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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // DailyTask milik satu Task
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

        // DailyTask terhubung ke Project Item (Checklist Item)
    public function item()
    {
        return $this->belongsTo(ProjectItem::class, 'project_item_id');
    }


    // DailyTask milik satu Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // RELASI MANY PROJECT (jika dipakai)
    public function projects()
    {
        return $this->belongsToMany(
            Project::class,
            'daily_task_project',
            'daily_task_id',
            'project_id'
        );
    }

    // DailyTask dikerjakan satu staff
    public function staff()
    {
        return $this->belongsTo(User::class, 'assigned_to_staff_id');
    }

    // Alias staff (dipakai di controller/view)
    public function assignedToStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to_staff_id');
    }

    // Aktivitas upload / komentar
    public function activities()
    {
        return $this->hasMany(TaskActivity::class, 'daily_task_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR: PROGRESS BERDASARKAN STATUS
    |--------------------------------------------------------------------------
    */
    public function getStatusBasedProgressAttribute(): int
    {
        return match ($this->status) {
            'Selesai'           => 100,
            'Menunggu Validasi' => 75,
            'Lanjutkan'         => 70,
            'Revisi'            => 60,
            'Dikerjakan'        => 50,
            'Belum Dikerjakan'  => 25,
            default             => 0,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | DEFAULT STATUS SAAT MEMBUAT TASK
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->status)) {
                // aman untuk semua flow
                $task->status = 'Belum Dikerjakan';
            }
        });
    }
}
