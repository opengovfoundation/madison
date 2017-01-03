<?php

namespace App\Http\Requests\Document;

use App\Models\Doc as Document;
use App\Http\Controllers\DocumentController;
use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'order' => 'in:created_at,updated_at,title,activity',
            'order_dir' => 'in:ASC,DESC',
            'publish_state' => 'array|in:' . implode(',', DocumentController::validPublishStatesForQuery()),
            'discussion_state' => 'array|in:' . implode(',', Document::validDiscussionStates()),
            'sponsor_id.*' => 'integer',
            'category_id.*' => 'integer',
            'category' => 'string',
            'limit' => 'integer',
            'page' => 'integer',
            'title' => 'string|nullable',
        ];
    }
}
