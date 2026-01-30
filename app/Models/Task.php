<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    // dari code versi 2 (biar aman semua field lama)
    protected $guarded = [];

    // dari code versi 1 (dipakai controller lama)
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'user_id',   // penting untuk penanggung jawab task
        'order',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Task milik satu project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Task bisa dikerjakan banyak divisi
    public function divisions()
    {
        return $this->belongsToMany(
            Division::class,
            'division_task_pivot',
            'task_id',
            'division_id'
        );
    }

    // Task punya banyak daily task
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

    /*
    |--------------------------------------------------------------------------
    | GLOBAL ORDERING (drag & drop)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('order', 'asc');
        });
    }
}
