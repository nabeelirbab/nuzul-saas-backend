<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_number',
        'code',
    ];

    protected $guarded = [
        'id',
        'created_at',
    ];

    protected $hidden = [
        'code',
        'id',
        'updated_at',
        'created_at',
    ];
}
