<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'division_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // RELASI: User (staff/kadiv) memiliki satu divisi
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // RELASI: User (manager) bisa membuat banyak project
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    // RELASI: User (staff) bisa ditugaskan banyak tugas harian
    public function assignedDailyTasks()
    {
        return $this->hasMany(DailyTask::class, 'assigned_to_staff_id');
    }

    // RELASI: User bisa membuat banyak aktivitas (komentar/upload)
    public function taskActivities()
    {
        return $this->hasMany(TaskActivity::class);
    }

    // RELASI: User (staff/admin) bisa menerima banyak tugas mendadak
    public function receivedAdHocTasks()
    {
        return $this->hasMany(AdHocTask::class, 'assigned_to_id');
    }

    // RELASI: User (manager/kadiv) bisa memberikan banyak tugas mendadak
    public function givenAdHocTasks()
    {
        return $this->hasMany(AdHocTask::class, 'assigned_by_id');
    }
}
