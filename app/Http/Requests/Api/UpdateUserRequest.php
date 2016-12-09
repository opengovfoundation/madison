<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\Request;
use App\Models\Doc;
use Auth;
use App\Models\User;

class UpdateUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        return $user == $this->user;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
