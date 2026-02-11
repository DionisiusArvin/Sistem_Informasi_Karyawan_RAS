<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\DataChanged;

class AdHocTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'assigned_to_id',
        'assigned_by_id',
        'due_date',
        'status',
        'file_path',
        'link',
        'notes',
        'weight', // ⬅️ tambahan
    ];

    /**
     * AUTO REALTIME 🔥
     * Setiap data berubah → broadcast
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

    // RELASI: Satu tugas mendadak diberikan kepada satu user
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    // RELASI: Satu tugas mendadak diberikan oleh satu user
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    // Agar fitur activity / upload tidak error
    public function activities()
    {
        return $this->hasMany(\App\Models\TaskActivity::class, 'ad_hoc_task_id');
    }
}
