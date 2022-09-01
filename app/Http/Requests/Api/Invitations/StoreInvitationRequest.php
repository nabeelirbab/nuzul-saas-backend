<?php

namespace App\Http\Requests\Api\Invitations;

use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $tenant = Tenant::find(tenant()->id);

        return Role::COMPANY_OWNER === (string) $tenant->users->first()->pivot->company_role_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role_id' => 'required|in:4,5',
        ];
    }
}
