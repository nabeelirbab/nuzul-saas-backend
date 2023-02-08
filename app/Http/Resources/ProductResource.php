<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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

            'type' => $this->type,

            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,

            'price' => $this->price,
            'price_monthly_recurring' => $this->price_monthly_recurring,
            'price_quarterly_recurring' => $this->price_quarterly_recurring,
            'price_yearly_recurring' => $this->price_yearly_recurring,

            'tax_percentage' => $this->tax_percentage,
            'status' => $this->status,
            'is_private' => $this->is_private,
        ];
    }
}
