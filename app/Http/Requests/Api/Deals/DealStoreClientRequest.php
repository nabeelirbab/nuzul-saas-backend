<?php

namespace App\Http\Requests\Api\Deals;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class DealStoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'property_id' => [
                'nullable',
                new TenantPropertyRule($this),
                'exists:properties,id',
            ],
            'mobile_number' => [
                'required',
                'digits_between:8,15',
            ],
        ];
    }
}
