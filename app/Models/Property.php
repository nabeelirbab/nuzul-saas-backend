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

    public function images()
    {
        return $this->morphMany(TenantUpload::class, 'reference');
    }

    public function owner()
    {
        return $this->belongsTo(TenantContact::class, 'tenant_contact_id', 'id');
    }
}
