<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Events\DataChanged;

class AdminTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'due_date',
        'assigned_by_manager_id',
        'assigned_to_admin_id',
        'status',
        'file_path',
        'link',
        'notes',
    ];

    /**
     * AUTO REALTIME 🔥
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

    /**
     * Relasi ke user (manager) yang memberikan tugas.
     */
    public function assignedByManager()
    {
        return $this->belongsTo(User::class, 'assigned_by_manager_id');
    }

    /**
     * Relasi ke project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi ke user (admin) yang ditugaskan.
     */
    public function assignedToAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_to_admin_id');
    }
}
