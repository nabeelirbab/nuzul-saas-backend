<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'member_id' => $this->id,
            'mobile_number' => $this->user->mobile_number,
            'name' => $this->user->name,
            'role' => [
                'role_id' => $this->role->id,
                'name_ar' => $this->role->name_ar,
                'name_en' => $this->role->name_en,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
