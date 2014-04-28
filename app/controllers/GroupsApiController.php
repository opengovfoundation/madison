<?php

class GroupsApiController extends ApiController
{
	public function __construct()
	{
		parent::__construct();
	
		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}
	
	public function getVerify()
	{
		$this->beforeFilter('admin');
		
		$groups = Group::where('status', '!=', Group::STATUS_ACTIVE)->get();
		
		return Response::json($groups);
	}
	
	public function postVerify()
	{
		$this->beforeFilter('admin');
		
		$request = Input::get('request');
		$status = Input::get('status');
		
		if(!Group::isValidStatus($status)) {
			throw new \Exception("Invalid value for veirfy request");
		}
		
		$group = Group::where('id', '=', $request['id'])->first();
		
		if(!$group) {
			throw new \Exception("Invalid Group");
		}
		
		$group->status = $status;
		
		return Response::json($group->save());
	}
}