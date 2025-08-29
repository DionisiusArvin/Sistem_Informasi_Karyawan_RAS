<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdHocTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'assigned_to_id',
        'due_date',
        'assigned_by_id',
        'status',
        'file_path', // <-- Tambah
        'notes',     // <-- Tambah
    ];

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
}