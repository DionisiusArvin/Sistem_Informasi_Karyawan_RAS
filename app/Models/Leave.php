<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\DataChanged;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'division_id',
        'start_date',
        'end_date',
        'reason',
        'type',
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


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
