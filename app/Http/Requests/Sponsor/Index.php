<?php

namespace App\Http\Requests\Sponsor;

use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'limit' => 'integer',
            'name' => 'string|nullable',
            'user_id.*' => 'integer',
            'order' => 'in:created_at,updated_at,name',
            'order_dir' => 'in:ASC,DESC',
            'page' => 'integer',
            'statuses' => 'array|in:' . implode(',', Sponsor::getStatuses()),
        ];
    }
}
