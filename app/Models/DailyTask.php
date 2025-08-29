<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_id',
        'name',
        'due_date',
        'status',
        'assigned_to_staff_id',
        'progress',
        'completion_status',
    ];

    // RELASI: Satu tugas harian adalah bagian dari satu tugas utama
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // RELASI: Satu tugas harian dikerjakan oleh satu user (staff)
    public function assignedToStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to_staff_id');
    }

    // RELASI: Satu tugas harian memiliki banyak aktivitas (komentar/upload)
    public function activities()
    {
        return $this->hasMany(TaskActivity::class);
    }

    public function getStatusBasedProgressAttribute(): int
    {
        return match ($this->status) {
            'Selesai' => 100,
            'Menunggu Validasi' => 75,
            'Revisi' => 60,
            'Dikerjakan' => 50,
            'Belum Dikerjakan' => 25,
            default => 0, // 'Tersedia' atau status lain
        };
    }
}