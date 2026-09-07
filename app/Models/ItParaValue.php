<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItParaValue extends Model
{
    use HasFactory;

    protected $table = 'it_para_values';

    protected $fillable = [
        'parameter_id',
        'investigationtest_id',
        'value',
        'created_on',
        'created_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameter_id');
    }

    public function investigationTest()
    {
        return $this->belongsTo(InvestigationTest::class, 'investigationtest_id');
    }
}
