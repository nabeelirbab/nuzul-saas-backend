<?php

namespace App\Http\Requests\Api\Deals;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class DealStoreRequest extends FormRequest
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
            'tenant_contact_id' => [
                'required',
                'exists:tenant_contacts,id',
            ],
            'category' => [
                'required',
                'in:residential,commercial',
            ],
            'purpose' => [
                'required',
                'in:rent,buy',
            ],
            'type' => [
                'required',
                'in:villa,building_apartment,villa_apartment,land,duplex,townhouse,mansion,villa_floor,farm,istraha,store,office,storage,building',
            ],
        ];
    }
}
