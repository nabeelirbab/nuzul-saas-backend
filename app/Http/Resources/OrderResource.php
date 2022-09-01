<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'package_price_monthly' => $this->package_price_monthly,
            'package_price_yearly' => $this->package_price_yearly,
            'package_tax' => $this->package_tax,
            'tax_amount' => $this->tax_amount,
            'total_amount' => $this->total_amount,
            'period' => $this->period,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'transactions' => $this->transactions,
        ];
    }
}
