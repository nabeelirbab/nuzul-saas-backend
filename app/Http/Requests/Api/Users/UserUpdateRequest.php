<?php

namespace App\Http\Requests\Api\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'name' => 'required|max:50|regex:/^[\pL\s\-]+$/u',
            'gender' => 'required|in:male,female,undefined',
            'email' => 'required|email|max:50|unique:users,email,'.auth()->user()->id,
        ];
    }
}
