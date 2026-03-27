<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BroadcastsDataChanges;

class Schedule extends Model
{
    use BroadcastsDataChanges;

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
            static::broadcastDataChanged($division);
        });

        static::updated(function ($division) {
            static::broadcastDataChanged($division);
        });

        static::deleted(function ($division) {
            static::broadcastDataChanged($division->id);
        });
    }
}
