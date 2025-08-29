<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'daily_task_id',
        'user_id',
        'activity_type',
        'notes',
        'file_path',
        'link_url', // <-- Tambahkan ini
    ];

    // RELASI: Satu aktivitas adalah bagian dari satu tugas harian
    public function dailyTask()
    {
        return $this->belongsTo(DailyTask::class);
    }

    // RELASI: Satu aktivitas dibuat oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}