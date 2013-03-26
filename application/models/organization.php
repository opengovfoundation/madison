<?php
class Organization extends Eloquent{
	public static $timestamp = true;
	
	public function users(){
		return $this->has_many('User');
	}
}


