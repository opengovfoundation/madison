<?php

namespace App\Http\Requests\User;

use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class SponsorsIndex extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $currentUser = $this->user();
        $userEditing = $this->route()->parameter('user');

        return $currentUser && ($userEditing == $currentUser || $currentUser->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'limit' => 'integer',
        ];
    }
}
