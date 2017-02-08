<?php

namespace App\Http\Requests\Setting\SiteSettings;

use App\Http\Requests\AdminRequest;
use App\Http\Controllers\SettingController;

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
            'date_format' => 'in:default,' . implode(',', array_keys(SettingController::validDateFormats())),
            'time_format' => 'in:default,' . implode(',', array_keys(SettingController::validTimeFormats())),
        ];
    }
}
