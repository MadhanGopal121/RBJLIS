<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    protected $fillable = [
        'lab_id',
        'name',
        'price',
        'lab_price',
        'created_by',
        'created_on',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
        'lab_price' => 'decimal:2',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function profileTests()
    {
        return $this->hasMany(ProfileTest::class, 'profile_id');
    }
}
