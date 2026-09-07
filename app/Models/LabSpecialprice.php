<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabSpecialprice extends Model
{
    use HasFactory;

    protected $table = 'lab_specialprices';

    protected $fillable = [
        'lab_id',
        'child_lab_id',
        'diagnosticestest_id',
        'mastertest_id',
        'sp_price',
        'created_by',
        'created_on',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sp_price' => 'decimal:2',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function diagnosticstest()
    {
        return $this->belongsTo(Diagnosticstest::class, 'diagnosticestest_id');
    }

    public function mastertest()
    {
        return $this->belongsTo(Mastertest::class, 'mastertest_id');
    }
}
