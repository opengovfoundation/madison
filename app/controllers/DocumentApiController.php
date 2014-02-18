<?php
/**
 * 	Controller for Document actions
 */
class DocumentApiController extends ApiController{

	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}	
	
	public function getRecent($query = null){
		$recent = 10;

		if(isset($query)){
			$recent = $query;
		}

		$docs = Doc::take(10)->orderBy('updated_at', 'DESC')->get();

		foreach($docs as $doc){
			$doc->setActionCount();
		}

		return Response::json($docs);
	}

	public function getCategories($query = null){

		return Response::json(array('tag1', 'tag2', 'tag3', 'tag4'));
	}
}

