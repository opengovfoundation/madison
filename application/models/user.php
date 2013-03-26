<?php
class User extends Eloquent{
	public static $timestamp = true;
	
	public function notes(){
		return $this->has_many('Note');
	}
	
	public function organization(){
		return $this->belongs_to('Organization');
	}
}

