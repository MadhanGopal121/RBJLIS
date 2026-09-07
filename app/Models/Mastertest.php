<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mastertest extends Model
{
    use HasFactory;

    protected $table = 'mastertests';

    protected $fillable = [
        'department_id',
        'name',
        'reference_val',
        'units',
        'price',
        'method',
        'notes',
        'specimen_time',
        'created_by',
        'created_on',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function diagnosticstests()
    {
        return $this->hasMany(Diagnosticstest::class, 'm_testid');
    }
}
