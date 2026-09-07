<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabDepartment extends Model
{
    use HasFactory;

    protected $table = 'lab_departments';

    protected $fillable = [
        'lab_id',
        'department_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
