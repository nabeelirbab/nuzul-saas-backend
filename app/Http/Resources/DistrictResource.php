<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'city_id' => $this->city_id,
            'city_name_ar' => $this->city->name_ar,
            'city_name_en' => $this->city->name_en,
            'country_name_en' => $this->city->country->name_en,
            'country_name_ar' => $this->city->country->name_ar,
            // 'boundaries' => $this->boundaries,
        ];
    }
}
