<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantUpload extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'reference_id', 'reference_type', 'tenant_id'];

    public function reference()
    {
        return $this->morphTo();
    }
}
