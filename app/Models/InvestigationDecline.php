<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationDecline extends Model
{
    use HasFactory;

    protected $table = 'investigation_decline';

    protected $fillable = [
        'investigation_id',
        'investigation_test_id',
        'lab_id',
        'reason',
        'filepath',
        'declined_role',
        'created_by',
        'created_on',
    ];

    public function investigation()
    {
        return $this->belongsTo(Investigation::class, 'investigation_id');
    }

    public function investigationTest()
    {
        return $this->belongsTo(InvestigationTest::class, 'investigation_test_id');
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
