<?php

class Notification extends Eloquent
{
	const TYPE_EMAIL = "email";
	const TYPE_TEXT = "text";
	
	protected $table = 'notifications';
	
	public function group()
	{
		return $this->belongsTo('Group', 'group_id');
	}
	
	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
	
	static public function getActiveNotifications($event)
	{
		return static::where('event', '=', $event)->get();
	}
}