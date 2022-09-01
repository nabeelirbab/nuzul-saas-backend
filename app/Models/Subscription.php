<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'package_id',
        'start_date',
        'end_date',
        'status',
        'is_trial',
    ];

    public function scopeTrial($query)
    {
        return $query->where('is_trial', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
