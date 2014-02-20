<?php
/**
 * 	User Model
 */

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface{
	
	protected $hidden = array('password');

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

	public function getAuthIdentifier(){
		return $this->id;
	}

	public function getAuthPassword(){
		return $this->password;
	}

	public function getReminderEmail(){
		return $this->email;
	}

	//Notes this user has created
	public function notes(){
		//return $this->hasMany('Note');

		//This needs to return the notes the user has created from elasticsearch
		return true;
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

		if($this->user_level != 1){
			return false;
		}

		$meta = $this->user_meta()->where('meta_key', '=', 'admin_contact')->first();

		if(isset($meta)){
			$this->admin_contact = $meta->meta_value == '1' ? true : false;
		}else{
			$this->admin_contact = false;
		}
	}
	
	public function setSuggestions(){
		// $suggestions = $this->hasMany('Note')->where('type', '=', 'suggestion')->get();
		
		// foreach($suggestions as $suggestion){
		// 	$suggestion->orig_content = DocContent::find($suggestion->section_id)->content;
		// }
		
		// $this->suggestions = $suggestions;
		
		// return true;
		return true;
	}
}

