<?php

namespace App\Http\Requests\Api\Uploads\Tenants;

use Illuminate\Foundation\Http\FormRequest;

class PresignedURLRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'model' => 'required|in:property,tenant,user',
            'reference_id' => 'required_if:model,property,user',
            'extension' => 'required|in:png,jpg,jpeg',
        ];
    }
}
