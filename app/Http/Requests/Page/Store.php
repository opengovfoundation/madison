<?php

namespace App\Http\Requests\Page;

use App\Http\Requests\AdminRequest;

class Store extends AdminRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nav_title' => 'required'
        ];
    }

}
