<?php
/**
 * 	User Model
 */

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class User extends Eloquent implements UserInterface, RemindableInterface{
	
	//TODO: Shouldn't this go before the class definition?
	use Zizaco\Entrust\HasRole;
	
	protected $hidden = array('password', 'token', 'last_login', 'updated_at');
	protected $softDelete = true;

	/**
	*	Validation rules
	*/
	protected static $rules = array(
	  'save' => array(
      'fname'	=> 'required',
      'lname'	=> 'required'
		),
	  'create' => array(
	    'email'			=> 'required|unique:users',
	    'password'	=> 'required',
	  ),
	  'social-signup'	=> array(
	    'email'			=> 'required|unique:users',
	    'oauth_vendor'	=> 'required',
	    'oauth_id'			=> 'required',
	    'oauth_update'	=> 'required'
		),
		'twitter-signup'	=> array(
      'oauth_vendor'	=> 'required',
      'oauth_id'			=> 'required',
      'oauth_update'	=> 'required'
    ),
    'update'	=> array(
      'email'			=> 'required|unique:users',
      'password'	=> 'required'
		),
		'verify'	=> array(
      'phone'			=> 'required'
		)
	);

	/**
	*	Custom error messages for certain validation requirements
	*/
	protected static $customMessages = array(
		'fname.required' => 'The first name field is required.',
		'lname.required' => 'The last name field is required.'
	);

	/**
	*	Constructor
	*
	*	@param array $attributes
	*	Extends Eloquent constructor
	*/
	public function __construct($attributes = array()){
		parent::__construct($attributes);
		$this->validationErrors = new MessageBag;
	}

	/**
	*	Save
	*
	*	Override Eloquent save() method
	*		Runs $this->beforeSave()
	*		Unsets:
	*			* $this->validationErrors
	*			* $this->rules
	*
	* @param array $options
	* @return bool
	*/
	public function save(array $options = array()){
		if(!$this->beforeSave()){
			return false;
		}

		//Don't want user model trying to save validationErrors field.  
		//	TODO: I'm sure Eloquent can handle this.  What's the setting for ignoring fields when saving?
		unset($this->validationErrors);
		unset($this->rules);
		unset($this->verify);

		return parent::save($options);
	}

	/**
	*	getErrors
	*
	*	Returns errors from validation
	* 
	*	@param void
	* @return MessageBag $this->validationErrors
	*/
	public function getErrors(){
		return $this->validationErrors;
	}

	public function verified(){
		$request = $this->user_meta()->where('meta_key', 'verify')->first();
		
		if(isset($request)){
			return $request->meta_value;	
		}else{
			return null;
		}
	}
	
	/**
	*	getDisplayName
	*
	*	Returns the user's display name
	*
	*	@param void
	* @return string 
	*/
	public function getDisplayName()
	{
		return "{$this->fname} {$this->lname}";
	}


	/**
	*	docs
	*
	*	Eloquent one-to-many relationship for User->Doc
	* @param void
	* @return TODO
	*/
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

	public function getSponsorStatus(){
		return $this->user_meta()->where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_SPONSOR)->first();
	}

	public function setIndependentAuthor($bool)
	{
		if($bool) {
			DB::transaction(function() {
				$metaKey = UserMeta::where('user_id', '=', $this->id)
							       ->where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_SPONSOR)
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
		                  ->where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_SPONSOR)
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

	/**
	*	beforeSave
	*
	*	Validates before saving.  Returns whether the User can be saved.
	*
	*	@param array $options
	* @return bool
	*/
	private function beforeSave(array $options = array()){
		$this->rules = $this->mergeRules();

		if(!$this->validate()){
			Log::error("Unable to validate user: ");
			Log::error($this->getErrors()->toArray());
			Log::error($this->attributes);
			return false;
		}

		$this->attributes = $this->autoHash();

		return true;
	}

	/**
	*	mergeRules
	*
	*	Merge the rules arrays to form one set of rules
	*
	* @param void
	* @return array $output
	*
	* @todo handle social login / signup rule merges
	*/
	private function mergeRules(){
		$rules = static::$rules;
		$output = array();

		//If we're updating the user
		if($this->exists){
			$merged = array_merge_recursive($rules['save'], $rules['update']);
			$merged['email'] = 'required|unique:users,email,' . $this->id;
		}
		//If we're signing up via Oauth
		else if (isset($this->oauth_vendor)){
			switch($this->oauth_vendor){
				case 'twitter':
					$merged = array_merge_recursive($rules['save'], $rules['twitter-signup']);
					break;
				case 'facebook':
				case 'linkedin':
					$merged = array_merge_recursive($rules['save'], $rules['social-signup']);
					break;
				default:
					throw new Exception("Unknown OAuth vendor: " . $this->oauth_vendor);
			}
		}
		//If we're creating a user via Madison
		else {
			$merged = array_merge_recursive($rules['save'], $rules['create']);
		}

		//Include verify rules if requesting verification
		if(isset($this->verify)){
			$merged = array_merge_recursive($merged, $rules['verify']);
		}

		foreach($merged as $field => $rules){
			if(is_array($rules)){
				$output[$field] = implode("|", $rules);
			}else{
				$output[$field] = $rules;
			}
		}

		return $output;
	}

	/**
	*	Validate
	*
	*	Validate input against merged rules
	*
	*	@param array $attributes
	* @return bool
	*/
	private function validate(){
		$validation = Validator::make($this->attributes, $this->rules, static::$customMessages);

		if($validation->passes()){
			return true;
		}

		$this->validationErrors = $validation->messages();

		return false;
	}

	/**
	*	autoHash
	*
	*	Auto hash passwords
	*
	*	@param void
	* @return array $this->attributes
	*/
	private function autoHash(){
		if(isset($this->attributes['password'])){
			if($this->attributes['password'] != $this->getOriginal('password')){
				$this->attributes['password'] = Hash::make($this->attributes['password']);
			}
		}

		return $this->attributes;
	}
}

