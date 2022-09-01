<?php

namespace App\Http\Requests\Api\Orders;

use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'package_id' => 'required|exists:packages,id',
            'period' => 'required|in:yearly,monthly',
            'payment_method' => 'required|in:bank_transfer,online',
        ];
    }
}
