<?php
/**
 * 	Controller for Document actions
 */
class Doc_Controller extends Base_Controller{
	public $restful = true;
	
	public function __construct(){
		parent::__construct();
	}
	
	//GET document view
	public function get_index($slug = null){
		//No document requested, list documents
		if(null == $slug){
			$docs = Doc::all();
			return View::make('doc.index')->with('docs', $docs);
		}
		
		try{
			//Add document reader
			Asset::add('reader', 'js/reader.js');
			
			//Retrieve requested document
			$doc = Doc::where_slug($slug)->first();
			
			if(!isset($doc)){
				return Response::error('404');
			}
			
			/*
			//Retrieve top-level document suggestions
			$suggestions = Note::with('user')
								->where_type('suggestion')
								->where('parent_id', 'IS', DB::raw('NULL'))
								->where('doc_id', '=', $doc->id)
								->order_by('likes')
								->get();
								
			//Retrieve top-level document comments
			$comments = Note::with('user')
								->where_type('comment')
								->where('parent_id', 'IS', DB::raw('NULL'))
								->where('doc_id', '=', $doc->id)
								->order_by('likes')
								->get();
			*/
			$notes = Note::with('user')
								->where('parent_id', 'IS', DB::raw('NULL'))
								->where('doc_id', '=', $doc->id)
								->order_by('likes')
								->get();
			
			//Render view and return
			return View::make('doc.reader.index')
						->with('doc', $doc)
						->with('notes', $notes);
						//->with('comments', $comments);
						
		}catch(Exception $e){
			return Redirect::to('docs')->with('error', $e->getMessage());
		}
		return Response::error('404');
	}
}

