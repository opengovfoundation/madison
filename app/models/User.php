<?php
/**
 * 	User Model
 */

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface{
	
	protected $hidden = array('password');

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
		return $this->hasMany('Note');
	}
	
	//This user's organization
	public function organization(){
		return $this->belongsTo('Organization');
	}
	
	public function note_meta(){
		return $this->hasMany('NoteMeta');
	}
	
	public function setSuggestions(){
		$suggestions = $this->hasMany('Note')->where('type', '=', 'suggestion')->get();
		
		foreach($suggestions as $suggestion){
			$suggestion->orig_content = DocContent::find($suggestion->section_id)->content;
		}
		
		$this->suggestions = $suggestions;
		
		return true;
	}
	
	public function comments(){
		return $this->hasMany('Note')->where('type', '=', 'comment');
	}
}

