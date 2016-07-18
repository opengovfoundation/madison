<?php

namespace App\Http\Requests;

use App\Models\Role;
use Auth;

class AdminRequest extends Request
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
}
