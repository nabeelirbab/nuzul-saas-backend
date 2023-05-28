<?php

namespace App\Http\Requests\Api\Deals;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class DealUpdateRequest extends FormRequest
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
            'member_id' => [
                'nullable',
                'exists:tenant_user,id',
            ],
            'max_price' => [
                'nullable',
                'gt:min_price',
            ],
            'max_area' => [
                'nullable',
                'gt:min_area',
            ],
            'districts_ids' => [
                'nullable',
                'array',
            ],
            'districts_ids.*' => [
                'exists:districts,id',
            ],
            'rent_period' => [
                'required_if:purpose,rent',
            ],
            'stage' => [
                'required',
                'in:new,visit,negotiation,won,lost',
            ],
            'facade' => [
                'nullable',
                'in:north,east,south,west,north_east,north_west,south_east,south_west',
            ],
        ];
    }
}
