<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document as Requests;
use Illuminate\Http\Request;
use Response;

class TranslationController extends Controller
{
    public function index(Requests\Index $request)
    {
        $ret = [];
        foreach ($request->input('msg_id', []) as $msgId) {
            $ret[$msgId] = trans($msgId);
        }

        return Response::json($ret);
    }
}
