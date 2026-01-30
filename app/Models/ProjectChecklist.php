<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChecklist extends Model
{
    protected $fillable = [
        'project_id',
        'name',
    ];

    // RELASI: Checklist milik Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // RELASI: Checklist punya banyak item
    public function items()
    {
        return $this->hasMany(ProjectItem::class, 'project_checklist_id');
    }

    // ACCESSOR: Progress checklist berdasarkan rata-rata progress item
    public function getProgressAttribute()
    {
        if ($this->items->count() === 0) {
            return 0;
        }

        return round($this->items->avg('progress'));
    }
}
