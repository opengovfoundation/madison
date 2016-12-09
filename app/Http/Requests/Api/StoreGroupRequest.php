<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\Request;
use App\Models\Group;
use Auth;

class StoreGroupRequest extends Request
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
        return Group::$rules;
    }
}
