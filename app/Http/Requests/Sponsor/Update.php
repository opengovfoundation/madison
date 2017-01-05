<?php

namespace App\Http\Requests\Sponsor;

use App\Models\Sponsor;
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

        return $currentUser && (
            $this->sponsor->isSponsorOwner($currentUser->id) || $currentUser->isAdmin()
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|required|unique:sponsors,name,' . $this->sponsor->id,
            'display_name' => 'string|required|unique:sponsors,display_name,' . $this->sponsor->id,
            'address1' => 'string|required',
            'address2' => 'string',
            'city' => 'string|required',
            'state' => 'string|required',
            'postal_code' => 'string|required',
            'phone' => 'string',
        ];
    }

}
