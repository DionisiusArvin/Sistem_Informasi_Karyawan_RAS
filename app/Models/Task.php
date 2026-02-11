<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\DataChanged;

class Task extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    protected $guarded = [];

    protected $fillable = [
        'project_id',
        'jenis_tugas',
        'name',
        'description',
        'user_id',
        'order',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTO REALTIME + GLOBAL ORDER
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        // Global ordering (sudah ada)
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('order', 'asc');
        });

        // 🔥 Auto realtime
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

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function divisions()
    {
        return $this->belongsToMany(
            Division::class,
            'division_task_pivot',
            'task_id',
            'division_id'
        );
    }

    public function dailyTasks()
    {
        return $this->hasMany(DailyTask::class);
    }

    /*
    |--------------------------------------------------------------------------
    | PROGRESS CALCULATION
    |--------------------------------------------------------------------------
    */
    public function getProgressPercentage()
    {
        $total = $this->dailyTasks()->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->dailyTasks()
            ->where('status', 'Selesai')
            ->count();

        return round(($completed / $total) * 100);
    }
}
