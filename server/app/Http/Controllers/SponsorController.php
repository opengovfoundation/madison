<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\UserMeta;
use Response;
use Validator;
use Auth;
use Input;


class SponsorController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter('auth', array(
            'on' => array('post', 'put', 'delete'),
        ));
    }

    public function getAllSponsors()
    {
        $results = Doc::getAllValidSponsors();

        return Response::json($results);
    }
}
