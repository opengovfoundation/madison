<?php

class Group extends Eloquent
{
	public static $timestamp = true;
	protected $softDelete = true;
	
	const STATUS_ACTIVE = 'active';
	const STATUS_PENDING = 'pending';
	
	const ROLE_OWNER = 'owner';
	const ROLE_EDITOR ='editor';
	const ROLE_STAFF = 'staff';
	
	static public function getStatuses()
	{
		return array(static::STATUS_ACTIVE, static::STATUS_PENDING);
	}
	
	static public function getRoles()
	{
		return array(static::ROLE_OWNER, static::ROLE_EDITOR, static::ROLE_STAFF);
	}
	
	public function members() 
	{
		return $this->hasMany('GroupMember');
	}
	
	public function findMember($userId) {
		return GroupMember::where('user_id', '=', $userId)->first();
	}
	
	public function isGroupOwner($userId) {
		$groupMember = $this->findMember($userId);

		return $groupMember->role == static::ROLE_OWNER;
	} 
}