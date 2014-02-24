<?php
/**
 * 	Controller for Document actions
 */
class DocController extends BaseController{
	public $restful = true;
	
	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}
	
	//GET document view
	public function index($slug = null){
		//No document requested, list documents
		if(null == $slug){
			$docs = Doc::all();
			
			$data = array(
				'docs'			=> $docs,
				'page_id'		=> 'docs',
				'page_title'	=> 'All Documents'
			);
			
			return View::make('doc.index', $data);
		}
		
		try{
			
			//Retrieve requested document
			$doc = Doc::where('slug', $slug)->first();
			
			if(!isset($doc)){
				App::abort('404');
			}
			
			//Set data array
			$data = array(
				'doc'			=> $doc,
				'page_id'		=> strtolower(str_replace(' ','-', $doc->title)),
				'page_title'	=> $doc->title
			);				
			
			//Render view and return
			return View::make('doc.reader.index', $data);
						
		}catch(Exception $e){
			return Redirect::to('docs')->with('error', $e->getMessage());
		}
		App::abort('404');
	}

	public function getSearch(){
		$q = Input::get('q');

    	$results = Doc::search(urldecode($q));
    	//$results = json_decode($results);

    	$docs = array();

    	foreach($results['hits']['hits'] as $result){
    		$doc = Doc::find($result['_source']['id']);
    		array_push($docs, $doc);
    	}

    	$data = array(
    		'page_id' 		=> 'doc-search',
    		'page_title' 	=> 'Search Results',
    		'results'			=> $docs,
    		'query'			=> $q
    	);

    	return View::make('doc.search.index', $data);

	}

	public function postSupport($doc){
		$input = Input::get();

		$supported = (bool)$input['support'];

		$docMeta = DocMeta::where('user_id', Auth::user()->id)->where('meta_key', '=', 'support')->where('doc_id', '=', $doc)->first();


		if(!isset($docMeta)){
			$docMeta = new DocMeta();

			$docMeta->doc_id = $doc;
			$docMeta->user_id = Auth::user()->id;
			$docMeta->meta_key = 'support';
			$docMeta->meta_value = (string)$supported;

			$docMeta->save();
		}
		elseif($docMeta->meta_value == (string)$supported){
			$docMeta->delete();
			$supported = null;
		}else{
			$docMeta->doc_id = $doc;
			$docMeta->user_id = Auth::user()->id;
			$docMeta->meta_key = 'support';
			$docMeta->meta_value = (string)$supported;

			$docMeta->save();
		}

		return Response::json(array('support' => $supported));
	}
}

