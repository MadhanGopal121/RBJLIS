<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationTestResult extends Model
{
    use HasFactory;

    protected $table = 'investigation_test_results';

    protected $fillable = [
        'investigation_id',
        'investigation_test_id',
        'parameter_id',
        'result',
        'is_bold',
        'iscritical',
        'sort',
        'type',
        'created_by',
        'created_on',
    ];

    protected $casts = [
        'is_bold' => 'boolean',
        'iscritical' => 'integer',
        'sort' => 'integer',
    ];

    public function investigation()
    {
        return $this->belongsTo(Investigation::class, 'investigation_id');
    }

    public function investigationTest()
    {
        return $this->belongsTo(InvestigationTest::class, 'investigation_test_id');
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameter_id');
    }
}
