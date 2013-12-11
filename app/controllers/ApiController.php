<?php
/**
 * 	Controller for Document actions
 */
class ApiController extends BaseController{
	
	protected $es; // ElasticSearch client

	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth');

		$params = array();
		$params['hosts'] = array('localhost:9200');

		$this->es = new Elasticsearch\Client($params);
	}
	
	public function getIndex(){
		$api = array(
			'message' 	=> 'Madison API',
			'links'		=> array(
			    'annotation'	=> array(
			    	'read'		=> array(
			    		'url'	=> action('AnnotationApiController@getIndex')
			    	)
			    )	
			)
		);

		return Response::json($api);
	}
}

