<?php

namespace App\Http\Requests\Document;

use App\Models\User;
use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->user()->can('admin_manage_documents')) {
            // If there's a sponsor
            if ($this->input('sponsor_id')) {
                $sponsor = Sponsor::find($this->input('sponsor_id'));

                return $sponsor && $sponsor->isActive() && $sponsor->canUserCreateDocument($this->user());
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'sponsor_id' => 'integer',
        ];
    }
}
