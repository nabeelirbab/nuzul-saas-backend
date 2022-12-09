<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'category',
        'purpose',
        'type',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
