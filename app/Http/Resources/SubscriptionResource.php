<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'package' => [
                'name_ar' => $this->package->name_ar,
                'name_en' => $this->package->name_en,
            ],
            'start_date' => $this->start_date,
            'end_date' => $this->start_date,
            'status' => $this->status,
            'is_trial' => $this->is_trial,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
