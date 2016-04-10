<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Role;
use Auth;

class DestroyPageRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        return $user && $user->hasRole(Role::ROLE_ADMIN);
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
