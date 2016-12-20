<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $currentUser = $this->user();
        $userEditing = $this->route()->parameter('user');

        return $currentUser && ($userEditing == $currentUser || $currentUser->isAdmin());
    }

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
            'new_password' => 'string|min:6|confirmed',
        ];
    }

}
