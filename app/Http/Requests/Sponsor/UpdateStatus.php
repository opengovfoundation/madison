<?php

namespace App\Http\Requests\Sponsor;

use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatus extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'in:' . implode(',', Sponsor::getStatuses()),
        ];
    }
}
