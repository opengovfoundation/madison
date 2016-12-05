<?php

namespace App\Http\Requests;

use App\Models\Role;
use Auth;

class DocAccessEditRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        $doc = null;
        foreach (['doc', 'docTrashed'] as $key) {
            if (!empty($this->route()->parameter($key))) {
                $doc = $this->route()->parameter($key);
                break;
            }
        }

        if (empty($doc)) {
            return false;
        }

        return $user && $doc->canUserEdit($user);
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
