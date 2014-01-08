<?php
/**
 * 	Base controller for catch-alls
 */
class BaseController extends Controller {
	
	protected $es;

	public function __construct(){
		$docs = Doc::orderBy('updated_at', 'desc')->take(10)->get();
		View::share('docs', $docs);

		$params = array('hosts' => array('localhost:9200'));

		$this->es = new Elasticsearch\Client($params);
	}
}
