<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class DistrictUpdateRequest extends FormRequest
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
            'city_id' => 'required|exists:cities,id'.$request['id'],
            'name_ar' => 'required|max:50,'.$request['id'],
            'name_en' => 'required|max:50,'.$request['id'],
            'boundaries' => 'required'.$request['id'],
        ];
    }
}
