<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Transaction extends Model
{
    use BelongsToPrimaryModel;
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tenant_id',
        'total_amount_with_tax',
        'status',
        'payment_method',
        'response',
        'reference_number',
    ];

    public function getRelationshipToPrimaryModel(): string
    {
        return 'order';
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
