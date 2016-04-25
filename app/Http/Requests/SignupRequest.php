<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SignupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // anyone can sign up
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|email',
            'password' => 'required',
            'fname'    => 'required',
            'lname'    => 'required',
        ];
    }

    /**
     * Get the attribute names for use in error messages.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'fname' => 'first name',
            'lname' => 'last name',
        ];
    }
}
