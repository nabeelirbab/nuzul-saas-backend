<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
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
            'mobile_number' => $this->mobile_number,
            'status' => $this->status,
            'workspace' => [
                'name_ar' => $this->tenant->name_ar,
                'name_en' => $this->tenant->name_en,
            ],
            'role' => [
                'role_id' => $this->role->id,
                'name_ar' => $this->role->name_ar,
                'name_en' => $this->role->name_en,
            ],
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
        ];
    }
}
