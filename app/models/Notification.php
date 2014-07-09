<?php

class Notification extends Eloquent
{
	const TYPE_EMAIL = "email";
	const TYPE_TEXT = "text";
	
	protected $table = 'notifications';
	protected $softDelete = false;
	public    $timestamps = false;
	
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
	
	static public function getValidNotifications()
	{
		return array(
			OpenGovEvent::DOC_COMMENT_COMMENTED => "When a comment is made on a comment",
			OpenGovEvent::DOC_COMMENTED => "When a document is commented on",
			OpenGovEvent::DOC_EDITED => "When a document is edited",
			OpenGovEvent::NEW_DOCUMENT => "When a new document is created",
			OpenGovEvent::NEW_USER_SIGNUP => "When a new user signs up",
			OpenGovEvent::VERIFY_REQUEST_ADMIN => "When a new admin verification is requested",
			OpenGovEvent::VERIFY_REQUEST_GROUP => "When a new group verification is requested",
			OpenGovEvent::VERIFY_REQUEST_USER => "When a new independent author verification is requested"
		);
	}
	
	static public function addNotificationForUser($event, $user_id, $type = self::TYPE_EMAIL)
	{
		$notification = static::where('user_id', '=', $user_id)
							  ->where('event', '=', $event)
							  ->where('type', '=', $type)
							  ->first();
		
		if($notification) {
			return;
		}
		
		$notification = new static();
		$notification->event = $event;
		$notification->user_id = $user_id;
		$notification->type = $type;
		
		return $notification->save();
	}
	
	static public function addNotificationForGroup($event, $group_id, $type = self::TYPE_EMAIL)
	{
		$notification = static::where('group_id', '=', $group_id)
							  ->where('event', '=', $event)
							  ->where('type', '=', $type)
							  ->first();
		
		if($notification) {
			return;
		}
		
		$notification = new static();
		$notification->event = $event;
		$notification->group_id = $group_id;
		$notification->type = $type;
		
		return $notification->save();
	}
	
	static public function addNotificationForAdmin($event, $type = self::TYPE_EMAIL)
	{
		$notification = static::where('user_id', '=', null)
							  ->where('group_id', '=', null)
							  ->where('event', '=', $event)
							  ->where('type', '=', $type)
							  ->first();
		
		if($notification) {
			return;
		}
		
		$notification = new static();
		$notification->event = $event;
		$notification->group_id = null;
		$notification->user_id = null;
		$notification->type = $type;
		
		return $notification->save();
	}
	
	static public function removeNotificationForAdmin($event, $type = self::TYPE_EMAIL)
	{
		$notification = static::where('user_id', '=', null)
							  ->where('group_id', '=', null)
							  ->where('event', '=', $event)
							  ->where('type', '=', $type)
							  ->first();
		
		if($notification) {
			$notification->delete();
		}
	}
	
	static public function removeNotificationForUser($event, $user_id, $type = self::TYPE_EMAIL)
	{
		$notification = static::where('user_id', '=', $user_id)
							  ->where('event', '=', $event)
							  ->where('type', '=', $type)
							  ->first();
		
		if($notification) {
			$notification->delete();
		}
	}
	
	static public function removeNotificationForGroup($event, $group_id, $type = self::TYPE_EMAIL)
	{
		$notification = static::where('group_id', '=', $group_id)
							  ->where('event', '=', $event)
							  ->where('type', '=', $type)
							  ->first();
		
		if($notification) {
			$notification->delete();
		}
	}
	
}