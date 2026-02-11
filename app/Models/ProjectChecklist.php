<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\DataChanged;

class ProjectChecklist extends Model
{
    protected $fillable = [
        'project_id',
        'name',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTO REALTIME 🔥
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::created(function ($division) {
            broadcast(new DataChanged($division));
        });

        static::updated(function ($division) {
            broadcast(new DataChanged($division));
        });

        static::deleted(function ($division) {
            broadcast(new DataChanged($division->id));
        });
    }

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
