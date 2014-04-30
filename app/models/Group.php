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
	
	static public function isValidStatus($status) {
		switch($status) {
			case static::STATUS_ACTIVE:
			case static::STATUS_PENDING:
				return true;
		}
		
		return false;
	}
	
	static public function isValidRole($role) 
	{
		switch($role) {
			case static::ROLE_EDITOR:
			case static::ROLE_OWNER:
			case static::ROLE_STAFF:
				return true;
		}
		
		return false;
	}
	
	static public function getRoles($forHtml = false)
	{
		if($forHtml) {
			return array(
				static::ROLE_OWNER => static::ROLE_OWNER,
				static::ROLE_EDITOR => static::ROLE_EDITOR,
				static::ROLE_STAFF => static::ROLE_STAFF
			);
		}
		
		return array(static::ROLE_OWNER, static::ROLE_EDITOR, static::ROLE_STAFF);
	}
	
	public function members() 
	{
		return $this->hasMany('GroupMember');
	}
	
	static public function findByMemberId($memberId)
	{
		$groupMember = GroupMember::where('id', '=', $memberId)->first();
		
		if(!$groupMember) {
			return null;
		}
		
		return static::where('id', '=', $groupMember->group_id)->first();
	}
	
	public function findMemberByUserId($userId) 
	{
		if(!isset($this->id) || empty($this->id)) {
			throw new \Exception("You must have a group ID set in order to search for members");
		}
		
		return GroupMember::where('user_id', '=', $userId)->where('group_id','=',$this->id)->first();
	}
	
	public function isGroupOwner($userId) 
	{
		$groupMember = $this->findMemberByUserId($userId);
		
		return $groupMember->role == static::ROLE_OWNER;
	} 
	
	public function addMember($userId, $role = null) 
	{
		$groupMember = $this->findMemberByUserId($userId);
		
		if(!$groupMember) {
			
			if(is_null($role)) {
				throw new \Exception("You must provide a role if adding a new member");
			}
			
			if(!isset($this->id) || empty($this->id)) {
				throw new \Exception("The group must have a ID set in order to add a member");
			}
			
			$groupMember = new GroupMember();
			$groupMember->user_id = Auth::user()->id;
			$groupMember->role = Group::ROLE_OWNER;
			$groupMember->group_id = $this->id;
			
		} else {
			if(!is_null($role)) {
				$groupMember->role = $role;
			}
		}
		
		$groupMember->save();
	}
}