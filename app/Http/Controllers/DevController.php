<?php

namespace App\Http\Controllers;


class DevController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function testEvent()
    {
        Event::fire(MadisonEvent::TEST, Auth::user());

        var_dump("HERE");
        exit;
    }
}
