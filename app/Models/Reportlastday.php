<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reportlastday extends Model
{
    use HasFactory;

    protected $table = 'reportlastday';

    protected $fillable = [
        'bill_id',
        'unique_id',
        'name',
        'age',
        'gender',
        'phone',
        'total_amount',
        'payment_amount',
        'paymenttype',
        'testnames',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];
}
