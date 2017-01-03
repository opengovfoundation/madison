<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\Request;
use App\Models\Sponsor;
use Auth;

class UpdateSponsorRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        $sponsor = Sponsor::find($this->sponsor);

        $changing_status = ($this->input('status') && $sponsor->status !== $this->input('status'));

        if ($changing_status) {
            return $user->hasRole('Admin');
        } else {
            return $sponsor->isSponsorOwner($user->id) || $user->hasRole('Admin');
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
