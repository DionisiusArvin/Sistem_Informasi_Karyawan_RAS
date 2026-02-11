<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\DataChanged;

class Schedule extends Model
{
    protected $fillable = [
        'kepala_divisi',
        'name',
        'description',
        'date',
        'status',
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
}
