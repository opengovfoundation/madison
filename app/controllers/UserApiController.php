<?php
/**
 * 	Controller for User actions
 */
class UserApiController extends ApiController{

	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}

	public function getUser($user){
		$user->load('docs', 'user_meta', 'comments', 'annotations');

		return Response::json($user);
	}
	
	public function getVerify(){
		$this->beforeFilter('admin');

		$requests = UserMeta::where('meta_key', 'verify')->with('user')->get();

		return Response::json($requests);
	}

	public function postVerify(){
		$this->beforeFilter('admin');

		$request = Input::get('request');
		$status = Input::get('status');

		$accepted = array('pending', 'verified', 'denied');

		if(!in_array($status, $accepted)){
			throw new Exception('Invalid value for verify request: ' . $status);
		}

		$meta = UserMeta::find($request['id']);

		$meta->meta_value = $status;

		$ret = $meta->save();

		return Response::json($ret);
	}

	public function getAdmins(){
		$this->beforeFilter('admin');

		$adminRole = Role::where('name', 'Admin')->first();
		$admins = $adminRole->users()->get();

		foreach($admins as $admin){
			$admin->admin_contact();
		}

		return Response::json($admins);
	}

	public function postAdmin(){
		$admin = Input::get('admin');

		$user = User::find($admin['id']);

		if(!isset($user)){
			throw new Exception('User with id ' . $admin['id'] . ' could not be found.');
		}

		$user->admin_contact($admin['admin_contact']);

		return Response::json(array('saved' => true));
	}

	public function getSupport($user, $doc){
		$docMeta = DocMeta::where('user_id', $user->id)->where('meta_key', '=', 'support')->where('doc_id', '=', $doc)->first();

		$supports = DocMeta::where('meta_key', '=', 'support')->where('meta_value', '=', '1')->where('doc_id', '=', $doc)->count();
		$opposes = DocMeta::where('meta_key', '=', 'support')->where('meta_value', '=', '')->where('doc_id', '=', $doc)->count();

		if(isset($docMeta)){
			return Response::json(array('support' => $docMeta->meta_value, 'supports' => $supports, 'opposes' => $opposes));
		}else{
			return Response::json(array('support' => null, 'supports' => $supports, 'opposes' => $opposes));
		}

		
	}
}

