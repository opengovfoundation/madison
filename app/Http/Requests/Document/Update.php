<?php

namespace App\Http\Requests\Document;

use App\Models\Doc as Document;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends Edit
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $document = null;
        foreach (['document', 'documentTrashed'] as $key) {
            if (!empty($this->route()->parameter($key))) {
                $document = $this->route()->parameter($key);
                break;
            }
        }

        return [
            'title' => 'string|required',
            'slug' => [
                'regex:/[0-9a-z-]+/',
                Rule::unique('docs', 'slug')->ignore($document->id),
            ],
            'publish_state' => 'in:' . implode(',', Document::validPublishStates()),
            'discussion_state' => 'in:' . implode(',', Document::validDiscussionStates()),
            'sponsor_id' => 'integer',
            'category_id.*' => 'integer',
            'featured-image' => 'image',
        ];
    }
}
