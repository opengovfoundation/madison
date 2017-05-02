<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if user is admin or not.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() && $this->user()->isAdmin();
    }
}
