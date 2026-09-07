<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationTest extends Model
{
    use HasFactory;

    protected $table = 'investigation_test';

    protected $fillable = [
        'investigation_id',
        'test_id',
        'package_id',
        'is_emergency',
        'specimen_time',
        'specimen_by',
        'test_time',
        'test_by',
        'result',
        'notes',
        'authenticated_by',
        'authenticated_time',
        'approved_by',
        'approved_time',
        'is_reportedgenerated',
        'print_by',
        'print_time',
        'is_declined',
        'declined_media',
        'declined_reason',
        'declined_by',
        'status',
        'created_by',
        'created_on',
    ];

    protected $casts = [
        'is_emergency' => 'boolean',
        'is_reportedgenerated' => 'boolean',
        'is_declined' => 'boolean',
        'specimen_time' => 'datetime',
        'test_time' => 'datetime',
        'authenticated_time' => 'datetime',
        'approved_time' => 'datetime',
        'print_time' => 'datetime',
    ];

    public function investigation()
    {
        return $this->belongsTo(Investigation::class, 'investigation_id');
    }

    public function diagnosticstest()
    {
        return $this->belongsTo(Diagnosticstest::class, 'test_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'package_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function specimenCollector()
    {
        return $this->belongsTo(User::class, 'specimen_by');
    }

    public function tester()
    {
        return $this->belongsTo(User::class, 'test_by');
    }

    public function authenticator()
    {
        return $this->belongsTo(User::class, 'authenticated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function results()
    {
        return $this->hasMany(InvestigationTestResult::class, 'investigation_test_id');
    }

    public function declines()
    {
        return $this->hasMany(InvestigationDecline::class, 'investigation_test_id');
    }
}
