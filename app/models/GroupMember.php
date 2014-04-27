<?php

class GroupMember extends Eloquent
{
	public static $timestamp = true;
	protected $softDelete = true;
	
	public function user()
	{
		return $this->belongsTo('User');
	}
	
	public function group()
	{
		return $this->belongsTo('Group');
	}
}