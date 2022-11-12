<?php

namespace App\Http\Requests\Api\Tenants;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class TenantUpdateRequest extends FormRequest
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
            'name_ar' => 'required|max:50|regex:/^[\pL\s\-]+$/u'.$request['id'],
            'name_en' => 'required|max:50|regex:/^[\pL\s\-]+$/u'.$request['id'],
        ];
    }
}
