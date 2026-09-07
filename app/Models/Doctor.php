<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';

    protected $fillable = [
        'lab_id',
        'doctor_name',
        'clinic_name',
        'phone',
        'email',
        'discount',
        'created_by',
        'created_on',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'discount' => 'decimal:2',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function investigations()
    {
        return $this->hasMany(Investigation::class, 'doctor_id');
    }
}
