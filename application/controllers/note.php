<?php
/**
 * 	Controller for note actions
 */	

class Note_Controller extends Base_Controller{
	public $restful = true;
	
	public function __construct(){
		parent::__construct();
		
		//Require the user to be signed in to create, update notes
		$this->filter('before', 'auth')->on(array('post', 'put'));
		
		//Run CSRF filter before all POSTs and PUTs
		$this->filter('before', 'csrf')->on('post');
	}
	
	/**
	 * 	GET note view
	 */
	public function get_index($id = null){
		//Return 404 if no id is passed
		if($id == null){
			return Response::error('404');
		}
		
		//Invalid note id
		$note = Note::find($id);
		if(!isset($note)){
			return Response::error('404');
		}
		
		//Retrieve note information
		$user = $note->user()->first();
		$doc_content = $note->doc_content()->first();
		$child_notes = $note->note_children()->get();
		
		//Render view and return to user
		return View::make('note.index')
					->with('note', $note)
					->with('user', $user)
					->with('doc_content', $doc_content)
					->with('child_notes', $child_notes);
		
	}
	
	/**
	 * 	Update note information
	 * 		ie. Likes, dislikes, flags, etc.
	 */
	public function put_index($id = null){
		//No note id requested
		if($id == null){
			$msg = 'There was an error processing your request';
			if(!Request::ajax()){
				return Redirect::to('docs')->with('error', $msg);
			}else{
				return json_encode(array('success'=>false, 'msg'=>$msg));
			}
		}
		try{
			//Get requested note
			$note = Note::find($id);
			if(!isset($note)){
				throw new Exception('Incorrect note id.');
			}
			
			//Retrieve user
			$user = Auth::user();
			if(!isset($user)){
				throw new Exception('Must be logged in to update notes');
			}
			
			$json_data = Input::json();
			
			$csrf_token = $json_data->csrf_token;
			if($csrf_token !== Session::token()){
				throw new Exception('Request was forged');
			}
			
			$meta_type = $json_data->meta_type;
			$user_id = Auth::user()->id;
			
			if($note->add_note_feedback($meta_type, $user_id)){
				$msg = "Note $meta_type saved successfully";
			}else{
				$msg = "Note $meta_type removed successfully";
			}
			
			$toReturn = array(
				'success'	=> true,
				'msg'		=> $msg,
				'likes'		=> $note->likes,
				'dislikes'	=> $note->dislikes,
				'flags'		=> $note->flags
			);
			
			return json_encode($toReturn);
		}catch(Exception $e){
			if(Request::ajax()){
				return json_encode(array('success'=>false, 'msg'=>$e->getMessage()));
			}else{
				return Redirect::to('note/' . $id)->with('error', $e->getMessage());
			}
			
		}
	}
	
	/**
	 * 	Create new note
	 */
	public function post_index($id = null){
		//Retrieve POST values
		$doc_id = Input::get('doc_id');
		$section_id = Input::get('section_id');
		$parent_id = Input::get('parent_id');
		$type = Input::get('type');
		$note_content = Input::get('note_content');
		$note_details = Input::all();
		
		//Note post validation rules
		$rules = array('doc_id'			=> 'required',
						'section_id'	=> 'required',
						'type'			=> 'required',
						'note_content'	=> 'required'
		);
		
		//Validate note post
		$validation = Validator::make($note_details, $rules);
		if($validation->fails()){
			$doc = Doc::find($doc_id);
			if(isset($doc)){
				return Redirect::to('doc/' . $doc->slug)->with_input()->with_errors($validation);
			}else{
				return Redirect::to('docs')->with_errors($validation)->with('error', 'Unable to redirect to document');
			}
		}
		
		//Retrieve correct document
		$doc = Doc::find($doc_id);
		if(!isset($doc)){
			return Redirect::to('docs/' . $doc->slug)->with('error', 'Unable to add note to document.');
		}
		
		try{
			//Retrieve user id
			$user_id = Auth::user()->id;
			if(!isset($user_id)){
				$msg = 'Error retrieving user id.';
				
				if(!Request::ajax()){
					if(isset($doc)){
						return Redirect::to('docs/' . $doc->slug)->with_input()->with('error', $msg);
					}
					else{
						return Redirect::to('docs')->with('error', $msg);
					}
				}else{
					return json_encode(array('success'=>false, 'msg'=>$msg));
				}
			}
			
			//Create new note
			$note = new Note();
			$note->user_id = $user_id;
			$note->section_id = $section_id;
			$note->doc_id = $doc_id;
			if($parent_id != 0){//parent_id equals null if none passed (top-level content)
				$note->parent_id = $parent_id;
			}
			$note->type = $type;
			$note->content = $note_content;
			$note->save();
			
			if($id == null){
				return Redirect::to('doc/' . $doc->slug)->with('success_message', ucwords($type) . ' saved successfully.');
			}
			else{
				return Redirect::to('note/' . $note->parent_id)->with('success_message', ucwords($type) . ' saved successfully');
			}
			
		}catch(Exception $e){
			if(isset($doc)){
				return Redirect::to('doc/' . $doc->slug)->with_input()->with('error', 'Error saving note: ' . $e->getMessage());
			}else{
				return Redirect::to('docs')->with('error', 'Error saving note: ' . $e->getMessage());
			}
		}
	}
}

