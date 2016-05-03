<?php

namespace App\Http\Requests;

use App\Http\Requests\AdminRequest;
use App\Models\Role;
use Auth;

class DestroyPageRequest extends AdminRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return parent::authorize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Whole object required for proper PUT route
        return [];
    }
}
