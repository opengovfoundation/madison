<?php
/**
 * 	Controller for Document actions
 */
class Doc_Controller extends Base_Controller{
	public $restful = true;
	
	public function __construct(){
		parent::__construct();
		
		Asset::add('bill-reader', 'js/bill-reader.js');
		Asset::add('note-votes', 'js/note.js');
	}
	
	//GET document view
	public function get_index($slug = null){
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
			//Add document reader
			Asset::add('reader', 'js/reader.js');
			
			//Retrieve requested document
			$doc = Doc::where_slug($slug)->first();
			
			if(!isset($doc)){
				return Response::error('404');
			}
			
			if(Auth::check()){
				$note_data = array('user');
			}else{
				$note_data = 'user';
			}
			
			
			$notes = Note::with($note_data)
								->where('parent_id', 'IS', DB::raw('NULL'))
								->where('doc_id', '=', $doc->id)
								->order_by('likes', 'desc')
								->get();
						
			if(Auth::check()){
				foreach($notes as $note){
					$note->setUserMeta();
				}
			}					
			
			//Set data array
			$data = array(
				'doc'			=> $doc,
				'notes'			=> $notes,
				'page_id'		=> strtolower(str_replace(' ','-', $doc->title)),
				'page_title'	=> $doc->title
			);				
			
			//Render view and return
			return View::make('doc.reader.index', $data);
						
		}catch(Exception $e){
			return Redirect::to('docs')->with('error', $e->getMessage());
		}
		return Response::error('404');
	}
}

