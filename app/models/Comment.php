<?php
class Comment extends Eloquent{
	protected $table = 'comments';

	public function doc(){
		return $this->belongsTo('Doc', 'doc_id');
	}

	public function user(){
		return $this->belongsTo('User', 'user_id');
	}
}

