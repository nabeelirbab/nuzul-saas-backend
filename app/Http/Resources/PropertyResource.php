<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'category' => $this->category,
            'purpose' => $this->purpose,
            'type' => $this->type,
            'year_built' => $this->year_built,
            'street_width' => $this->street_width,
            'selling_price' => $this->selling_price,
            'rent_price_monthly' => $this->rent_price_monthly,
            'rent_price_quarterly' => $this->rent_price_quarterly,
            'rent_price_semi_annually' => $this->rent_price_semi_annually,
            'rent_price_annually' => $this->rent_price_annually,
            'area' => $this->area,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'number_of_floors' => $this->number_of_floors,
            'unit_floor_number' => $this->unit_floor_number,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'dining_rooms' => $this->dining_rooms,
            'living_rooms' => $this->living_rooms,
            'majlis_rooms' => $this->majlis_rooms,
            'maid_rooms' => $this->maid_rooms,
            'driver_rooms' => $this->driver_rooms,
            'mulhaq_rooms' => $this->mulhaq_rooms,
            'storage_rooms' => $this->storage_rooms,
            'basement_rooms' => $this->basement_rooms,
            'elevators' => $this->elevators,
            'pools' => $this->pools,
            'balconies' => $this->balconies,
            'kitchens' => $this->kitchens,
            'gardens' => $this->gardens,
            'parking_spots' => $this->parking_spots,
            'facade' => $this->facade,
            'is_kitchen_installed' => $this->is_kitchen_installed,
            'is_ac_installed' => $this->is_ac_installed,
            'is_parking_shade' => $this->is_parking_shade,
            'is_furnished' => $this->is_furnished,
            'cover_image_url' => $this->cover_image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'country' => $this->district ? [
                'id' => $this->district->city->country->id,
                'name_en' => $this->district->city->country->name_en,
                'name_ar' => $this->district->city->country->name_ar,
            ] : null,
            'city' => $this->district ? [
                'id' => $this->district->city->id,
                'name_en' => $this->district->city->name_en,
                'name_ar' => $this->district->city->name_ar,
            ] : null,
            'district' => $this->district ? [
                'id' => $this->district->id,
                'name_en' => $this->district->name_en,
                'name_ar' => $this->district->name_ar,
            ] : null,
            'images' => $this->images,
        ];
    }
}
