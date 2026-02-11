<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\DataChanged;

class TaskActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_task_id',
        'user_id',
        'activity_type',
        'notes',
        'file_path',
        'link_url',
        'progress_percent',
    ];

    protected $casts = [
        'progress_percent' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTO REALTIME 🔥
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::created(function ($activity) {
            broadcast(new DataChanged($activity));
        });

        static::updated(function ($activity) {
            broadcast(new DataChanged($activity));
        });

        static::deleted(function ($activity) {
            broadcast(new DataChanged($activity->id));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Satu aktivitas bagian dari satu DailyTask
    public function dailyTask()
    {
        return $this->belongsTo(DailyTask::class);
    }

    // Satu aktivitas dibuat oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
