<?php

namespace App\Http\Requests\Admin\FeaturedDocuments;

use App\Http\Requests\AdminRequest;

class Update extends AdminRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action' => 'required|in:up,down,remove',
        ];
    }
}
