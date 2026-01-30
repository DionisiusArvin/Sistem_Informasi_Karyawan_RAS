<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'kepala_divisi',
        'name',
        'description',
        'date',
        'status',
    ];
}
