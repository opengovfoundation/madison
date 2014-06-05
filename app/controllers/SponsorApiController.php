<?php

use Illuminate\Database\Eloquent\Collection;
class SponsorApiController extends ApiController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->beforeFilter('auth', array(
			'on' => array('post', 'put', 'delete')
		));
	}
	
	public function getAllSponsors()
	{
		$results = Doc::getAllValidSponsors();
		return Response::json($results);
	}
}