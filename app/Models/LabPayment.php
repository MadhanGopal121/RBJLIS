<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabPayment extends Model
{
    use HasFactory;

    protected $table = 'lab_payments';

    protected $fillable = [
        'lab_id',
        'investigation_id',
        'labtolab_id',
        'paymenttype',
        'payment_amount',
        'trans_number',
        'received_date',
        'received_by',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'received_date' => 'datetime',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function investigation()
    {
        return $this->belongsTo(Investigation::class, 'investigation_id');
    }

    public function labtolab()
    {
        return $this->belongsTo(Labtolab::class, 'labtolab_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
