<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticstest extends Model
{
    use HasFactory;

    protected $table = 'diagnosticstests';

    protected $fillable = [
        'lab_id',
        'department_id',
        'm_testid',
        'test_id',
        'test_code',
        'name',
        'reference_val',
        'units',
        'price',
        'lab_price',
        'method',
        'notes',
        'note',
        'default_notes',
        'default_note',
        'interpretation',
        'interpretation_img',
        'sample_type',
        'specimen_time',
        'created_by',
        'created_on',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
        'lab_price' => 'decimal:2',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function mastertest()
    {
        return $this->belongsTo(Mastertest::class, 'm_testid');
    }

    public function parameters()
    {
        return $this->hasMany(Parameter::class, 'diagnosticstests_id');
    }

    public function specialPrices()
    {
        return $this->hasMany(LabSpecialprice::class, 'diagnosticestest_id');
    }

    public function profileTests()
    {
        return $this->hasMany(ProfileTest::class, 'diagnosticstest_id');
    }

    public function investigationTests()
    {
        return $this->hasMany(InvestigationTest::class, 'test_id');
    }
}
