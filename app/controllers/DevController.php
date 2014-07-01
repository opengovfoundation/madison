<?php

class DevController extends Controller
{
	public function testEvent()
	{
		Event::fire(OpenGovEvent::TEST, Auth::user());
		
		var_dump("HERE");exit;
	}
}