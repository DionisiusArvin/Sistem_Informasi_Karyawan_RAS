<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectItem extends Model
{
    protected $fillable = [
        'project_checklist_id',
        'name',
    ];

    // RELASI: Item milik checklist
    public function checklist()
    {
        return $this->belongsTo(ProjectChecklist::class, 'project_checklist_id');
    }

    // RELASI: Item memiliki banyak daily task
    public function dailyTasks()
    {
        return $this->hasMany(DailyTask::class);
    }

    // ACCESSOR: Progress item berdasarkan daily task yang selesai
    public function getProgressAttribute()
    {
        $total = $this->dailyTasks()->count();
        $done  = $this->dailyTasks()->where('status', 'done')->count();

        if ($total === 0) {
            return 0;
        }

        return round(($done / $total) * 100);
    }
}
