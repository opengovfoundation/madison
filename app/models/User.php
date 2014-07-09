<?php
/**
 * 	User Model
 */

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\Collection;

class User extends Eloquent implements UserInterface, RemindableInterface{
	
	use Zizaco\Entrust\HasRole;
	protected $hidden = array('password', 'token', 'last_login', 'updated_at');
	//protected $fillable = array('id', 'email', 'fname', 'lname', 'user_level');
	protected $softDelete = true;

	public function verified(){
		$request = $this->user_meta()->where('meta_key', 'verify')->first();
		
		if(isset($request)){
			return $request->meta_value;	
		}else{
			return null;
		}
	}

	public function docs(){
		return $this->belongsToMany('Doc');
	}

	public function activeGroup() 
	{
		$activeGroupId = Session::get('activeGroupId');
		
		if($activeGroupId <= 0) {
			return new Group();
		}
		
		return Group::where('id', '=', $activeGroupId)->first();
	}
	
	public function groups() {
		return $this->belongsToMany('Group', 'group_members');
	}
	
	public function comments(){
		return $this->hasMany('Comment');
	}

	public function annotations(){
		return $this->hasMany('Annotation');
	}

	public function getAuthIdentifier(){
		return $this->id;
	}

	public function getAuthPassword(){
		return $this->password;
	}

	public function getReminderEmail(){
		return $this->email;
	}
	
	//This user's organization
	public function organization(){
		return $this->belongsTo('Organization');
	}
	
	public function note_meta(){
		return $this->hasMany('NoteMeta');
	}

	public function user_meta(){
		return $this->hasMany('UserMeta');
	}

	public function setIndependentAuthor($bool)
	{
		if($bool) {
			DB::transaction(function() {
				$metaKey = UserMeta::where('user_id', '=', $this->id)
							       ->where('meta_key', '=', 'independent_author')
								   ->first();
				
				if(!$metaKey) {
					$metakey = new UserMeta();
					$metaKey->user_id = $this->id;
					$metaKey->meta_key = 'independent_author';
				}
				
				$metaKey->meta_value = $bool ? 1 : 0;
				$metaKey->save();
				
				
			});
		}
	}
	
	public function admin_contact($setting = null){

		if(isset($setting)){
			$meta = $this->user_meta()->where('meta_key', '=', 'admin_contact')->first();

			if(!isset($meta)){
				$meta = new UserMeta();
				$meta->user_id = $this->id;
				$meta->meta_key = 'admin_contact';
				$meta->meta_value = $setting;
				$meta->save();

				return true;
			}else{
				$meta->meta_value = $setting;
				$meta->save();

				return true;
			}
		}

		if(!$this->hasRole('Admin')){
			return false;
		}

		$meta = $this->user_meta()->where('meta_key', '=', 'admin_contact')->first();

		if(isset($meta)){
			$this->admin_contact = $meta->meta_value == '1' ? true : false;
		}else{
			$this->admin_contact = false;
		}
	}

	public function doc_meta(){
		return $this->hasMany('DocMeta');
	}
	
	public function getValidSponsors()
	{
		$collection = new Collection();
		
		$groups = GroupMember::where('user_id', '=', $this->id)
						     ->whereIn('role', array(Group::ROLE_EDITOR, Group::ROLE_OWNER))
						     ->get();
		
		foreach($groups as $groupMember) {
			
			$collection->add($groupMember->group()->first());
		}
		
		$users = UserMeta::where('user_id', '=', $this->id)
		                  ->where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_AUTHOR)
		                  ->where('meta_value', '=', '1')
		                  ->get();
		
		foreach($users as $userMeta) {
			$collection->add($userMeta->user()->first());
		}

		return $collection;
	}
	
	static public function findByRoleName($role) 
	{
		return Role::where('name', '=', $role)->first()->users()->get();
	}
}

