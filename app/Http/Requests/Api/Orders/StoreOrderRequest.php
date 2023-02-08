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
        if (!$this->is_trial) {
            return [
                'products' => [
                    'required',
                    'array',
                ],
                'products.0.product_id' => [
                    'required',
                    'exists:products,id',
                ],
                'products.0.qty' => [
                    'required',
                    'gt:0',
                ],
                'period' => 'required|in:yearly,quarterly,one_time',
                'payment_method' => 'required|in:bank_transfer,online',
            ];
        }

        return [];
    }
}
