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

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}	
	
	//Route for /api/annotation/{id}
	//	Returns json annotation if id found,
	//		404 with error message if id not found,
	//		404 if no id passed
	public function getIndex($id = null){

		try{
			if($id !== null){
				$results = Annotation::find($this->es, $id);
			}else{
				$results = Annotation::all($this->es);
			}
		}catch(Elasticsearch\Common\Exceptions\Missing404Exception $e){
			App::abort(404, 'Id not found');
		}catch(Exception $e){
			App::abort(404, $e->getMessage());
		}

		return Response::json($results);
	}

	public function postIndex(){
		$body = Input::all();

		$annotation = new Annotation();
		$annotation->body($body); 

		$id = $annotation->save($this->es);

		return Redirect::to('/api/annotations/' . $id, 303);
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

		try{
			$ret = Annotation::delete($this->es, $id);
		}catch(Elasticsearch\Common\Exceptions\Missing404Exception $e){
			App::abort(404, 'Id not found');
		}catch(Exception $e){
			App::abort(404, $e->getMessage());
		}
		
		return Response::make(null, 204);

	}

	public function getSearch(){
		$es = $this->es;

		$uri = Input::get('uri');

		$params['index'] = "annotator";
		$params['type'] = "annotation";

		if($uri !== null){	
			$params['body']['query']['match']['uri'] = $uri;	
		}
		
		$results = $es->search($params);

		$total = $results['hits']['total'];

		$rows = array('rows' => array(), 'total'=>$total);

		foreach($results['hits']['hits'] as $result){
			array_push($rows['rows'], $result);
		}

		return Response::json($rows);
	}
}

