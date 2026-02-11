<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\DataChanged;

class DailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'project_id',
        'project_item_id',
        'name',
        'due_date',
        'status',
        'assigned_to_staff_id',
        'progress',
        'completion_status',
        'description',
        'weight',
    ];

    protected $casts = [
        'weight' => 'integer',
        'due_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTO REALTIME 🔥
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::created(function ($task) {
            broadcast(new DataChanged($task));
        });

        static::updated(function ($task) {
            broadcast(new DataChanged($task));
        });

        static::deleted(function ($task) {
            broadcast(new DataChanged($task->id));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function item()
    {
        return $this->belongsTo(ProjectItem::class, 'project_item_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projects()
    {
        return $this->belongsToMany(
            Project::class,
            'daily_task_project',
            'daily_task_id',
            'project_id'
        );
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'assigned_to_staff_id');
    }

    public function assignedToStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to_staff_id');
    }

    public function activities()
    {
        return $this->hasMany(TaskActivity::class, 'daily_task_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */
    public function getStatusBasedProgressAttribute(): int
    {
        if ($this->status === 'Selesai') {
            return 100;
        }

        return (int) $this->progress;
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
                $task->status = 'Belum Dikerjakan';
            }
        });
    }
}
