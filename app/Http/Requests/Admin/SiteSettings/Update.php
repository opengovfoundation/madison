<?php

namespace App\Http\Requests\Admin\SiteSettings;

use App\Http\Requests\AdminRequest;
use App\Http\Controllers\AdminController;

class Update extends AdminRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'madison.date_format' => 'in:default,' . implode(',', array_keys(AdminController::validDateFormats())),
            'madison.time_format' => 'in:default,' . implode(',', array_keys(AdminController::validTimeFormats())),
            'madison.google_analytics_property_id' => 'regex:/UA-[0-9]+-[0-9]+/',
        ];

        $keys = array_map(function ($key) {
            return str_replace('.', '_', $key);
        }, array_keys($rules));

        return array_combine($keys, $rules);
    }
}
