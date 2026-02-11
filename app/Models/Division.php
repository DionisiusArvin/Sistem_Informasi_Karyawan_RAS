<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\DataChanged;

class Division extends Model
{
    use HasFactory;

    protected $guarded = [];

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

    // RELASI: Satu divisi bisa memiliki banyak user (staff/kadiv)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // RELASI: Satu divisi bertanggung jawab atas banyak tugas utama
    public function tasks()
    {
        return $this->belongsToMany(
            Task::class,
            'division_task_pivot',
            'division_id',
            'task_id'
        );
    }
}
