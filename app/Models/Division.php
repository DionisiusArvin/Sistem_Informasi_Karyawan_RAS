<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BroadcastsDataChanges;

class Division extends Model
{
    use BroadcastsDataChanges, HasFactory;

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | AUTO REALTIME 🔥
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::created(function ($division) {
            static::broadcastDataChanged($division);
        });

        static::updated(function ($division) {
            static::broadcastDataChanged($division);
        });

        static::deleted(function ($division) {
            static::broadcastDataChanged($division->id);
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
