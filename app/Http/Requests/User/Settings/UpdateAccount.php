<?php

namespace App\Http\Requests\User\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccount extends \App\Http\Requests\User\Edit
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fname' => 'string|required',
            'lname' => 'string|required',
            'email' => 'email|required|unique:users',
        ];
    }

    public function messages()
    {
        return [
            'fname.required' => 'The first name field is required.',
            'lname.required' => 'The last name field is required.',
        ];
    }
}
