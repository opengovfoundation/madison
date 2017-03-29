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
        // If there's a sponsor
        if ($this->input('sponsor_id')) {
            $sponsor = Sponsor::find($this->input('sponsor_id'));

            return $this->user()->can('create', [\App\Models\Doc::class, $sponsor]);
        }

        return false;
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
            'sponsor_id' => 'required|integer',
        ];
    }
}
