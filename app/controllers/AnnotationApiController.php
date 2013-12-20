<?php
/**
 * 	Controller for Document actions
 */
class AnnotationApiController extends ApiController{
	
	protected $es; // ElasticSearch client
	protected $params = array(
		'index' => 'annotator',
		'type' 	=> 'annotation'
	);

	public function __construct(){
		parent::__construct();
	}	
	
	//Route for /api/annotation/{id}
	//	Returns json annotation if id found,
	//		404 with error message if id not found,
	//		404 if no id passed
	public function getIndex($id = null){
		$es = $this->es;
		$params = $this->params;
		
		//If no id requested, return 404
		if($id === null){
			
			App::abort(404, 'No annotation id passed.');
		}
		
		$params['id'] = $id;

		try{
			$results = $es->get($params);	
		}catch(Elasticsearch\Common\Exceptions\Missing404Exception $e){
			App::abort(404, 'Id not found');
		}catch(Exception $e){
			App::abort(404, $e->getMessage());
		}

		return $results;
	}

	public function postIndex(){
		$es = $this->es;
		$params = $this->params;

		$body = Input::all();
		//TODO: check required fields
		
		$params['body'] = $body;

		//TODO: add try-catch block here
		$ret = $es->index($params);

		return $ret;
	}

	public function putIndex($id = null){
		
		//If no id requested, return 404
		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		$es = $this->es;
		$params = $this->params;

		$body = Input::all();

		$params['id'] = $id;
		$params['body']['doc'] = $body;

		//TODO: check body values

		try{
			$results = $es->update($params);	
		}catch(Elasticsearch\Common\Exceptions\Missing404Exception $e){
			App::abort(404, 'Id not found');
		}catch(Exception $e){
			App::abort(404, $e->getMessage());
		}

		return $results;

	}

	public function deleteIndex($id = null){
		//If no id requested, return 404
		if($id === null){
			
			App::abort(404, 'No annotation id passed.');
		}

		$es = $this->es;
		$params = $this->params;

		$params['id'] = $id;

		try{
			$results = $es->delete($params);	
		}catch(Elasticsearch\Common\Exceptions\Missing404Exception $e){
			App::abort(404, 'Id not found');
		}catch(Exception $e){
			App::abort(404, $e->getMessage());
		}
		
		return $results;

	}

	public function getSearch(){
		$es = $this->es;

		$params['index'] = "annotator";
		$params['type'] = "annotation";
		$params['body']['query']['match_all'] = array();

		$results = $es->search($params);

		return $results;
	}
}

