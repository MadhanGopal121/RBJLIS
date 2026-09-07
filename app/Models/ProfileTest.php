<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileTest extends Model
{
    use HasFactory;

    protected $table = 'profile_tests';

    protected $fillable = [
        'profile_id',
        'diagnosticstest_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    public function diagnosticstest()
    {
        return $this->belongsTo(Diagnosticstest::class, 'diagnosticstest_id');
    }
}
