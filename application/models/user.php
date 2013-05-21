<?php
/**
 * 	User Model
 */
class User extends Eloquent{
	public static $timestamp = true;
	
	//Notes this user has created
	public function notes(){
		return $this->has_many('Note');
	}
	
	//This user's organization
	public function organization(){
		return $this->belongs_to('Organization');
	}
	
	public function note_meta(){
		return $this->has_many('NoteMeta');
	}
}

