<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Doc;

class UpdateDocumentRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO: Move middleware doc edit auth logic here
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
            'publish_state' => 'in:' . implode(',', Doc::validPublishStates()),
            'discussion_state' => 'in:' . implode(',', Doc::validDiscussionStates())
        ];
    }
}
