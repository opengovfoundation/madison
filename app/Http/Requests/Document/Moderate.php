<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class Moderate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $document = null;

        foreach (['document', 'documentTrashed'] as $key) {
            if (!empty($this->route()->parameter($key))) {
                $document = $this->route()->parameter($key);
                break;
            }
        }

        return $user && $document && $document->canUserEdit($user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
