<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CityUpdateRequest extends FormRequest
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
            'country_id' => 'required|exists:countries,id'.$request['id'],
            'region_id' => 'required|exists:regions,id'.$request['id'],
            'name_ar' => 'required|max:50|unique:cities,name_ar,'.$request['id'],
            'name_en' => 'required|max:50|unique:cities,name_en,'.$request['id'],
            'longitude' => 'required'.$request['id'],
            'latitude' => 'required'.$request['id'],
        ];
    }
}
