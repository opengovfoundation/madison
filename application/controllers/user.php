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
			return Redirect::back()->with('error', 'User page not found.');
		}
		
		
		
	}
	
}



