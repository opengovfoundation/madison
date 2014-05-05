<?php
/**
 * 	Controller for admin dashboard
 */
class DashboardController extends BaseController{
	
	public $restful = true;
	
	public function __construct(){
		//Filter to ensure user is signed in has an admin role
		$this->beforeFilter('admin');
		
		//Run csrf filter before all posts
		$this->beforeFilter('csrf', array('on'=>'post'));
		
		parent::__construct();
	}
	
	/**
	 * 	Dashboard Index View
	 */
	public function getIndex(){
		$data = array(
			'page_id'		=> 'dashboard',
			'page_title'	=> 'Dashboard'
		);

		return View::make('dashboard.index', $data);
	}
	
	
	public function getGroupverifications() 
	{
		$user = Auth::user();
		
		if(!$user->can('admin_verify_users')) {
			return Redirect::to('/dashboard')->with('message', "You do not have permission"); 
		}
		
		$groups = Group::where('status', '!=', Group::STATUS_ACTIVE)->get();
		
		$data = array(
			'page_id' => 'verify_groups',
			'page_title' => 'Verify Groups',
			'requests' => $groups
		);
		
		return View::make('dashboard.verify-group', $data);
	}
	
	/**
	 * 	Verification request view
	 */
	public function getVerifications(){
		
		$user = Auth::user();
		
		if(!$user->can('admin_verify_users')) {
			return Redirect::to('/dashboard')->with('message', "You do not have permission");
		}
		
		$requests = UserMeta::where('meta_key', 'verify')->with('user')->get();

		$data = array(
			'page_id'		=> 'verify_users',
			'page_title'	=> 'Verify Users',
			'requests'		=> $requests
		);

		return View::make('dashboard.verify-account', $data);
	}

	/**
	*	Settings page
	*/

	public function getSettings(){
		$data = array(
			'page_id'		=> 'settings',
			'page_title'	=> 'Settings',
		);

		$user = Auth::user();
		
		if(!$user->can('admin_manage_settings')) {
			return Redirect::to('/dashboard')->with('message', "You do not have permission");
		}
		
		return View::make('dashboard.settings', $data);
	}

	public function postSettings(){
		
		$user = Auth::user();
		
		if(!$user->can('admin_manage_settings')) {
			return Redirect::to('/dashboard')->with('message', "You do not have permission");
		}
		
		$adminEmail = Input::get('contact-email');

		$adminContact = User::where('email', '$adminEmail');

		if(!isset($adminContact)){
			return Redirect::back()->with('error', 'The admin account with this email was not found.  Please try a different email.');
		}
	}
}

