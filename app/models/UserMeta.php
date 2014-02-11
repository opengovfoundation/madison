<?php
/**
 * 	User meta model
 */
class UserMeta extends Eloquent{
	protected $table = 'user_meta';
	
	public function user(){
		return $this->belongsTo('User');
	}
	
}

