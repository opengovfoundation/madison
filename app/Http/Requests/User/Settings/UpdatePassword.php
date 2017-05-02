<?php

namespace App\Http\Requests\User\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePassword extends \App\Http\Requests\User\Edit
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'new_password' => 'string|min:6|confirmed',
        ];
    }
}
