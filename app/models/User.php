<?php
/**
 * 	User Model
 */

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface{
	
	use Zizaco\Entrust\HasRole;
	protected $hidden = array('password', 'token', 'last_login', 'created_at', 'updated_at');
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
}

