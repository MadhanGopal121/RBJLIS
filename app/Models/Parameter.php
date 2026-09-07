<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;

    protected $table = 'parameters';

    protected $fillable = [
        'diagnosticstests_id',
        'type',
        'name',
        'sort',
        'default_value',
        'units',
        'method',
        'created_by',
        'created_on',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort' => 'integer',
    ];

    public function diagnosticstest()
    {
        return $this->belongsTo(Diagnosticstest::class, 'diagnosticstests_id');
    }

    public function results()
    {
        return $this->hasMany(InvestigationTestResult::class, 'parameter_id');
    }

    public function itParaValues()
    {
        return $this->hasMany(ItParaValue::class, 'parameter_id');
    }
}
