<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investigation extends Model
{
    use HasFactory;

    protected $table = 'investigations';

    protected $fillable = [
        'lab_id',
        'patient_id',
        'doctor_id',
        'ltol_id',
        'refered_by',
        'clinicname',
        'total_amount',
        'balance_amount',
        'discount',
        'notes',
        'prescription',
        'status',
        'test_status',
        'delete_notes',
        'created_by',
        'created_on',
    ];

    protected $casts = [
        'status' => 'boolean',
        'total_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function labtolab()
    {
        return $this->belongsTo(Labtolab::class, 'ltol_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function investigationTests()
    {
        return $this->hasMany(InvestigationTest::class, 'investigation_id');
    }

    public function labPayments()
    {
        return $this->hasMany(LabPayment::class, 'investigation_id');
    }

    public function declines()
    {
        return $this->hasMany(InvestigationDecline::class, 'investigation_id');
    }
}
