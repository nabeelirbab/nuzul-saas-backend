<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'contact_id',
        'is_property_buyer',
        'is_property_owner',
        'city_id',
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

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
