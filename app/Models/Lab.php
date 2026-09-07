<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    use HasFactory;

    protected $table = 'labs';

    protected $fillable = [
        'name',
        'contactname',
        'shortname',
        'email',
        'phone',
        'logo',
        'letter_head',
        'lab_seal',
        'address',
        'user_count',
        'sub_from',
        'sub_to',
        'defult_notes',
        'upi_id',
        'upi_name',
        'enable_upi',
        'stripe_key',
        'stripe_secret',
        'stripe_webhook_secret',
        'enable_stripe',
        'sms_provider',
        'sms_api_key',
        'sms_sender_id',
        'enable_sms',
        'enable_email',
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_pass',
        'smtp_encryption',
        'created_by',
        'created_on',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'enable_upi' => 'boolean',
        'enable_stripe' => 'boolean',
        'enable_sms' => 'boolean',
        'enable_email' => 'boolean',
        'sub_from' => 'date',
        'sub_to' => 'date',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'lab_id');
    }

    public function diagnosticstests()
    {
        return $this->hasMany(Diagnosticstest::class, 'lab_id');
    }

    public function labDepartments()
    {
        return $this->hasMany(LabDepartment::class, 'lab_id');
    }

    public function labtolabs()
    {
        return $this->hasMany(Labtolab::class, 'lab_id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'lab_id');
    }

    public function investigations()
    {
        return $this->hasMany(Investigation::class, 'lab_id');
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class, 'lab_id');
    }
}
