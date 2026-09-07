<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'is_default',
        'price',
        'created_on',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function diagnosticstests()
    {
        return $this->hasMany(Diagnosticstest::class, 'department_id');
    }

    public function mastertests()
    {
        return $this->hasMany(Mastertest::class, 'department_id');
    }

    public function userDepartments()
    {
        return $this->hasMany(UserDepartment::class, 'department_id');
    }

    public function labDepartments()
    {
        return $this->hasMany(LabDepartment::class, 'department_id');
    }
}
