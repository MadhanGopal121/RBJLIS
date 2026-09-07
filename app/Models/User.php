<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'lab_id',
        'role_id',
        'phone',
        'signature',
        'designation',
        'permissions',
        'last_login',
        'created_on',
        'created_by',
        'status',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'status' => 'boolean',
        ];
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function userDepartments()
    {
        return $this->hasMany(UserDepartment::class, 'user_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'user_departments', 'user_id', 'department_id');
    }

    public function investigations()
    {
        return $this->hasMany(Investigation::class, 'created_by');
    }
}