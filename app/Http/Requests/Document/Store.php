<?php

namespace App\Http\Requests\Document;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->user()->can('admin_manage_documents')) {
            // If there's a group
            if ($this->input('group_id')) {
                $group = Group::find($this->input('group_id'));

                return $group && $group->isActive() && $group->canUserCreateDocument($request->user());
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required'
        ];
    }
}
