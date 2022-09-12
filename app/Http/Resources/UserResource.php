<?php

namespace App\Http\Resources;

use App\Models\Tenant;
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
            'gender' => $this->gender,
            'role' => [
                'role_id' => $this->role->id,
                'name_ar' => $this->role->name_ar,
                'name_en' => $this->role->name_en,
            ],
            'workspaces' => $this->tenants->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name_en' => $item->name_en,
                    'name_ar' => $item->name_ar,
                    'company_role' => [
                        'name_ar' => $item->pivot->role->id,
                        'name_ar' => $item->pivot->role->name_ar,
                        'name_en' => $item->pivot->role->name_en,
                    ],
                    'domain' => Tenant::find($item->id)->domains->first()->domain,
                ];
            }),
        ];
    }
}
