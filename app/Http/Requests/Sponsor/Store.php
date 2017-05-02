<?php

namespace App\Http\Requests\Sponsor;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|required|unique:sponsors',
            'display_name' => 'string|required|unique:sponsors',
            'address1' => 'string|required',
            'address2' => 'string',
            'city' => 'string|required',
            'state' => 'string|required',
            'postal_code' => 'string|required',
            'phone' => 'string',
        ];
    }

}
