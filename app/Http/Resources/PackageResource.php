<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'price_monthly' => $this->price_monthly,
            'price_yearly' => $this->price_yearly,
            'tax' => $this->tax,
            'status' => $this->status,
            'is_trial' => $this->is_trial,
        ];
    }
}
