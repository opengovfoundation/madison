<?php

namespace App\Http\Requests\Document;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class Edit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $doc = null;
        foreach (['document', 'documentTrashed'] as $key) {
            if (!empty($this->route()->parameter($key))) {
                $doc = $this->route()->parameter($key);
                break;
            }
        }

        return $user && $doc && $doc->canUserEdit($user);
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
