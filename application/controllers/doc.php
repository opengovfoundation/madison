<?php
class Doc_Controller extends Base_Controller{
	public $restful = true;
	
	public function __construct(){
		parent::__construct();
	}
	
	public function get_index($slug = null){
		if(null == $slug){
			$docs = Doc::all();
			return View::make('doc.index')->with('docs', $docs);
		}
		
		try{
			$doc = Doc::where_slug($slug)->first();
			return View::make('doc.reader')->with('doc', $doc);
		}catch(Exception $e){
			return Redirect::home()->with('error', $e->getMessage());
		}
		return Response::error('404');
	}
}

