<?php

class DevController extends Controller
{
	public function testEvent()
	{
		Event::fire(MadisonEvent::TEST, Auth::user());
		
		var_dump("HERE");exit;
	}
}