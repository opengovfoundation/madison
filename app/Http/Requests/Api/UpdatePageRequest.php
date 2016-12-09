<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\AdminRequest;
use App\Models\Role;
use Auth;

class UpdatePageRequest extends AdminRequest
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
        return [
            'url' => 'required',
            'page_title' => 'required',
            'nav_title' => 'required',
            'header' => 'required',
            'header_nav_link' => 'required',
            'footer_nav_link' => 'required',
            'external' => 'required'
        ];
    }
}
