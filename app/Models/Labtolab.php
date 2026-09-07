<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labtolab extends Model
{
    use HasFactory;

    protected $table = 'labtolab';

    protected $fillable = [
        'lab_id',
        'lab_name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address',
        'created_by',
        'created_on',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function investigations()
    {
        return $this->hasMany(Investigation::class, 'ltol_id');
    }

    public function labPayments()
    {
        return $this->hasMany(LabPayment::class, 'labtolab_id');
    }
}
