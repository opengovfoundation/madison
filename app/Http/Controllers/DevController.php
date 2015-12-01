<?php

namespace App\Http\Controllers;


class DevController extends Controller
{
    public function testEvent()
    {
        Event::fire(MadisonEvent::TEST, Auth::user());

        var_dump("HERE");
        exit;
    }
}
