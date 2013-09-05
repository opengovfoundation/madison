<?php
/**
 * 	Controller for user actions
 */
class User_Controller extends Base_Controller{
	public $restful = true;
	
	public function __construct(){
		parent::__construct();
	}
	
	public function get_index($id = null){
		
		if($id == null){
			return Response::error('404');
		}
		
		//Grab user by id
		$user = User::find($id)->with(array('comments', 'organization'));
		
		$user->setSuggestions();
		
		//Invalid user id
		if(!isset($user)){
			return Response::error('404');
		}
	
		//Set data array
		$data = array(
			'user'			=> $user,
			'page_id'		=> 'user_profile',
			'page_title'	=> $user->fname . ' ' . substr($user->lname, 0, 1) . "'s Profile"	
		);
	
		//Render view and return
		return View::make('user.index', $data);
	}
	
	public function get_edit($id=null){
		if(!Auth::check()){
			return Redirect::to('login')->with('error', 'Please log in to edit user profile');
		}else if(Auth::user()->id != $id){
			return Redirect::back()->with('error', 'You do not have access to that profile.');
		}else if($id == null){
			return Response::error('404');
		}
		
		//Set data array
		$data = array(
			'page_id'		=> 'edit_profile',
			'page_title'	=> 'Edit Your Profile'
		);
		
		return View::make('user.edit.index', $data);
	}
	
	public function put_edit($id=null){
		if(!Auth::check()){
			return Redirect::to('login')->with('error', 'Please log in to edit user profile');
		}else if(Auth::user()->id != $id){
			return Redirect::back()->with('error', 'You do not have access to that profile.');
		}else if($id == null){
			return Response::error('404');
		}
		
		$email = Input::get('email');
		$fname = Input::get('fname');
		$lname = Input::get('lname');
		$url = Input::get('url');
		$location = Input::get('location');
		$user_details = Input::all();
		
		if(Auth::user()->email != $email){
			$rules = array(
				'fname'		=> 'required',
				'lname'		=> 'required',
				'email'		=> 'required|unique:users'
			);
		}else{
			$rules = array(
				'fname'		=> 'required',
				'lname'		=> 'required'
			);
		}
		
		
		$validation = Validator::make($user_details, $rules);
		
		if($validation->fails()){
			return Redirect::to('user/edit/' . $id)->with_input()->with_errors($validation);
		}
		
		$user = User::find($id);
		$user->email = $email;
		$user->fname = $fname;
		$user->lname = $lname;
		$user->url = $url;
		$user->location = $location;
		$user->save();
		
		return Redirect::back()->with('success_message', 'Your profile has been updated.');
	}
	
	public function put_index($id = null){
		return Response::error('404');
	}
	
	public function post_index($id = null){
		return Response::error('404');
	}
}
