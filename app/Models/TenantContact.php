<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantContact extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'contact_id',
        'is_property_buyer',
        'is_property_owner',
        'district_id',
        'contact_name_by_tenant',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
