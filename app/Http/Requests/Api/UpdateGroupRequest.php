<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\Request;
use App\Models\Group;
use Auth;

class UpdateGroupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        $group = Group::find($this->group);

        $changing_status = ($this->input('status') && $group->status !== $this->input('status'));

        if ($changing_status) {
            return $user->hasRole('Admin');
        } else {
            return $group->isGroupOwner($user->id) || $user->hasRole('Admin');
        }
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
