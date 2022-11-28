<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Invitation extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'expires_at',
        'mobile_number',
        'tenant_id',
        'company_role_id',
        'status',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'company_role_id');
    }
}
