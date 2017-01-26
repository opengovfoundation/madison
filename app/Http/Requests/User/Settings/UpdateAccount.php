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
            'email' => 'email|required',
            'address1' => 'string',
            'address2' => 'string',
            'city' => 'string',
            'state' => 'string',
            'postal_code' => 'string',
            'phone' => 'string',
            'url' => 'url',
        ];
    }
}
