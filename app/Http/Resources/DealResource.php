<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'contact' => [
                'id' => $this->contact->id,
                'name' => $this->contact->contact_name_by_tenant,
                'mobile_number' => $this->contact->contact->mobile_number,
            ],
            'property' => null,
            'stage' => $this->stage,
            'category' => $this->category,
            'purpose' => $this->purpose,
            'type' => $this->type,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'min_area' => $this->min_area,
            'max_area' => $this->max_area,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'facade' => $this->facade,
            'is_kitchen_installed' => $this->is_kitchen_installed,
            'is_ac_installed' => $this->is_ac_installed,
            'is_furnished' => $this->is_furnished,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'districts' => $this->districts->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name_en' => $item->name_en,
                    'name_ar' => $item->name_ar,
                ];
            }),
        ];
    }
}
