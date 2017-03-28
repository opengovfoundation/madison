<?php

namespace App\Http\Requests\Admin\Sponsors;

use App\Models\Sponsor;
use App\Http\Requests\AdminRequest;

class PutStatus extends AdminRequest
{
    public function rules()
    {
        return [
            'status' => 'in:' . implode(',', Sponsor::getStatuses()),
        ];
    }
}
