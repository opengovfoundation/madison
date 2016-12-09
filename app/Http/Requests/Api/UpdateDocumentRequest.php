<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\Request;
use App\Models\Doc;

class UpdateDocumentRequest extends DocAccessEditRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'publish_state' => 'in:' . implode(',', Doc::validPublishStates()),
            'discussion_state' => 'in:' . implode(',', Doc::validDiscussionStates())
        ];
    }
}
