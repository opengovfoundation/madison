<?php
/**
 * 	Controller for Document actions
 */
class AnnotationApiController extends ApiController{
	
	protected $es; // ElasticSearch client

	public function __construct(){
		parent::__construct();
	}
	
	public function getIndex(){
		$es = $this->es;

		$params['index'] = "annotator";
		$params['type'] = "annotation";
		$params['body']['query']['match_all'] = array();

		$results = $es->search($params);

		return $results;
	}
}

