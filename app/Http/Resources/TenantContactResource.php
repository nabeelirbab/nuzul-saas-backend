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
            'mobile_number' => $this->contact->mobile_number,
            'email' => $this->contact->email,
            'is_property_buyer' => $this->is_property_buyer,
            'is_property_owner' => $this->is_property_owner,
            'city' => $this->city ? [
                'id' => $this->city->id,
                'name_ar' => $this->city->name_ar,
                'name_en' => $this->city->name_en,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
