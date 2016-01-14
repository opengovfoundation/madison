<?php

namespace App\Http\Controllers;

use App\Models\PublishState;
use Response;

class PublishStateApiController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllPublishStates()
    {
        $results = PublishState::get();
        return Response::json($results);
    }
}
