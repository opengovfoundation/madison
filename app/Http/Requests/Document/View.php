<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class View extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $document = null;
        foreach (['document', 'documentTrashed'] as $key) {
            if (!empty($this->route()->parameter($key))) {
                $document = $this->route()->parameter($key);
                break;
            }
        }

        return $document && $document->canUserView($this->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
