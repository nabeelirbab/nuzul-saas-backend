<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_contact_id',
        'tenant_id',
        'category',
        'purpose',
        'type',
    ];

    public function contact()
    {
        return $this->belongsTo(TenantContact::class, 'tenant_contact_id', 'id');
    }

    public function districts()
    {
        return $this->belongsToMany(District::class);
    }
}
