<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantContactResource extends JsonResource
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
            'name' => $this->contact_name_by_tenant,
            'is_property_buyer' => $this->is_property_buyer,
            'is_property_owner' => $this->is_property_owner,
            'distract' => [
                'id' => $this->district->id,
                'name_ar' => $this->district->name_ar,
                'name_en' => $this->district->name_en,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
