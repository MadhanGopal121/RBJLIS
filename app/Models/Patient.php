<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    protected $fillable = [
        'lab_id',
        'unique_id',
        'title',
        'name',
        'age',
        'gender',
        'phone',
        'email',
        'aadhar',
        'address',
        'created_by',
        'created_on',
        'updated_by',
        'updated_on',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function investigations()
    {
        return $this->hasMany(Investigation::class, 'patient_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
