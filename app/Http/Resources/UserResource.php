<?php

namespace App\Http\Resources;

use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'role' => $this->role->name_en,
            'companies' => $this->when(
                Role::COMPANY === $this->role->id,
                $this->companies->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name_en' => $item->name_en,
                    'name_ar' => $item->name_ar,
                    'active' => $item->active,
                    'company_role' => $item->pivot->role->name_en,
                ];
            }),
            ),
        ];
    }
}
