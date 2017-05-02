<?php

namespace App\Http\Requests\Page;

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
            'url' => 'string|required',
            'nav_title' => 'string|required',
            'header_nav_link' => 'boolean|required',
            'footer_nav_link' => 'boolean|required',
            'external' => 'boolean|required',

            // These not required if the page is external
            'page_title' => 'string' . ($this->input('external') === "1" ? "" : "|required"),
            'header' => 'string' . ($this->input('external') === "1" ? "" : "|required"),
            'page_content' => 'string' . ($this->input('external') === "1" ? "" : "|required"),
        ];
    }

}
