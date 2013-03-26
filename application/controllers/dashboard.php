<?php
class Dashboard_Controller extends Base_Controller{
	
	public $restful = true;
	
	public function __construct(){
		//Filter to ensure user is signed in and is user_level == 1
		$this->filter('before', 'admin');
		
		//Run csrf filter before all posts
		$this->filter('before', 'csrf')->on('post');
		
		parent::__construct();
	}
	
	/**
	 * 	Dashboard Index View
	 */
	public function get_index(){
		return View::make('dashboard.index');
	}
	
	/**
	 * 	Navigation Edit View
	 */
	public function get_nav(){
		Asset::add('edit-nav', 'js/edit-nav.js');
		$docs = Doc::all();
		$nav = Setting::where('meta_key', '=', 'nav')->first();
		if(isset($nav->meta_value)){
			$nav = unserialize($nav->meta_value);
		}
		
		return View::make('dashboard.edit-nav')
			->with('docs', $docs)
			->with('nav', $nav);
	}
	
	/**
	 * 	Save navigation menu
	 */
	public function post_nav(){
		$navInput = Input::get('nav');
		
		try{
			$nav = Setting::where('meta_key', '=', 'nav')->first();
			
			if(!isset($nav)){
				$nav = new Setting();
				$nav->meta_key = 'nav';
				$nav->meta_value = serialize($navInput);
				$nav->save();
			}
			
			echo json_encode(array('success'=>true, 'message'=>'Navigation Menu Saved Successfully')); 
		}catch (Exception $e){
			echo json_encode(array('success'=>false, 'message'=>'Failed to save navigation menu.'));
		}
	}
	
	/**
	 * 	Document Creation/List or Document Edit Views
	 */
	public function get_docs($id = ''){
		if($id == ''){
			$docs = Doc::all();
			return View::make('dashboard.docs')->with('docs', $docs);
		}
		else{
			$doc = Doc::find($id);
			if(isset($doc)){
				Asset::add('edit-doc', 'js/edit-doc.js');
				return View::make('dashboard.edit-doc')->with('doc', $doc);
			}
			else{
				return Response::error('404');
			}
		}
	}
	
	/**
	 * 	Post route for creating / updating documents
	 */
	public function post_docs($id = ''){
		//Creating new document
		if($id == ''){
			$title = Input::get('title');
			$slug = Input::get('slug');
			$doc_details = Input::all();

			$rules = array('title' => 'required',
							'slug' => 'required|unique:docs'
							);
			$validation = Validator::make($doc_details, $rules);
			if($validation->fails()){
				return Redirect::to('dashboard/docs')->with_input()->with_errors($validation);
			}

			try{
				$doc = new Doc();
				$doc->title = $title;
				$doc->slug = $slug;
				$doc->save();

				$starter = new DocContent();
				$starter->doc_id = $doc->id;
				$starter->content = "New Doc Content";
				$starter->save();

				$doc->init_section = $starter->id;
				$doc->save();

				return Redirect::to('dashboard/docs/' . $doc->id)->with('success_message', 'Document created successfully');
			}catch(Exception $e){
				return Redirect::to('dashboard/docs')->with_input()->with('error', $e->getMessage());
			}
		}
		else{
			return Response::error('404');
		}
	}
	
	public function put_docs($id = ''){
		$doc_items = Input::get('doc_items');
		$doc_items = json_decode($doc_items);
		
		
		foreach($doc_items as $item){
			try{
				$content = DocContent::find($item->id);
				$content->doc_id = $id;
				$content->parent_id = $item->parent_id == 0 ? null : $item->parent_id;
				$content->child_priority = $item->child_priority;
				$content->content = $item->content;
				$content->save();
			}catch (Exception $e){
				return Redirect::to('dashboard/docs/' . $id)->with('error', 'Error saving the document: ' . $e->getMessage());
			}
		}
		
		$deleted_ids = Input::get('deleted_ids');
		if(!empty($deleted_ids)){
			$deleted_ids = explode(',', $deleted_ids);
			$deleted_ids = array_unique($deleted_ids);
			
			foreach($deleted_ids as $deletedId){
				try{
					$toDelete = DocContent::find($deletedId);
					$toDelete->delete();
				}catch(Exception $e){
					return Redirect::to('dashboard/docs/' . $id)->with('error', 'Errors saving the document: ' . $e->getMessage());
				}
			}
		}
		
		return Redirect::to('dashboard/docs/' . $id)->with('success_message', 'Document Saved Successfully');
	}
	
	public function post_content($id = ''){
		
		if($id != ''){
			return Response::error('404');
		}
		$content_details = Input::all();
		
		$rules = array('doc_id' => 'required',
						'content' => 'required',
						'parent_id' => 'required'
						);
		$validation = Validator::make($content_details, $rules);
		if($validation->fails()){
			return json_encode(array('success'=>false, 'msg'=>'New content failed required fields'));
		}
		
		$doc_id = Input::get('doc_id');
		$content = Input::get('content');
		$parent_id = Input::get('parent_id');
		$child_priority = Input::get('child_priority');
		
		$doc_item = new DocContent();
		$doc_item->doc_id = $doc_id;
		$doc_item->content = $content;
		$doc_item->parent_id = $parent_id;
		$doc_item->child_priority = 0;
		
		try{
			$doc_item->save();
		}catch(Exception $e){
			return json_encode(array('success'=>false, 'msg'=>'Failure saving new content: ' . $e->getMessage()));
		}
		
		return json_encode(array('success'=>true, 'id' => $doc_item->id, 'msg' => 'Content created successfully'));
	}
	
	/**
	 * 	Verification request view
	 */
	public function get_verifications(){
		return View::make('dashboard.verify-account');
	}
	
	/**
	 * 	Post route for handling verification request responses
	 */
	public function post_verification(){
		
	}
}

