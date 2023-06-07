<?php

namespace App\Http\Resources;

use App\Models\Tenant;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
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
            'is_default' => (bool) $this->pivot->is_default,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'logo_url' => $this->logo_url,
            'company_role' => [
                'role_id' => $this->pivot->role->id,
                'name_ar' => $this->pivot->role->name_ar,
                'name_en' => $this->pivot->role->name_en,
            ],
            'domain' => Tenant::find($this->id)->domains->first()->domain,
        ];
    }
}
