<?php

use Illuminate\Database\Eloquent\Collection;
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

	public function docs(){
		return $this->belongsToMany('Doc');
	}
	
	public function getDisplayName()
	{
		return !empty($this->display_name) ? $this->display_name : !empty($this->name) ? $this->name : "";
	}
	
	public function getRoleId($role)
	{
		$role = strtolower($role);
		
		if(!static::isValidRole($role)) {
			throw new \Exception("Invalid Role");
		}
		
		return "group_{$this->id}_$role";
	}
	
	public function userHasRole($user, $role)
	{
		$roleId = $this->getRoleId($role);
		
		return $user->hasRole($roleId);
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
	
	protected function getPermissionsArray()
	{
		return array(
			array(
				'name' => "group_{$this->id}_create_document",
				'display_name' => "Create Documents"
			),
			array(
				'name' => "group_{$this->id}_edit_document",
				'display_name' => 'Edit Documents'
			),
			array(
				'name' => "group_{$this->id}_delete_document",
				'display_name' => "Delete Documents"
			),
			array(
				'name' => "group_{$this->id}_manage_document",
				'display_name' => "Manage Documents"
			)
		);
	}
	
	public function createRbacRules()
	{
		$this->destroyRbacRules();
		
		$ownerRole = new Role();
		$ownerRole->name = "group_{$this->id}_owner";
		$ownerRole->save();
		
		$permissions = $this->getPermissionsArray();
		
		$permIds = array();
		
		$permLookup = array();
		
		foreach($permissions as $perm) {
			$permModel = new Permission();
			
			foreach($perm as $key => $val) {
				$permModel->$key = $val;
			}
			
			$permModel->save();
			
			$permIds[] = $permModel->id;
			
			switch($perm['name']) {
				case "group_{$this->id}_create_document":
					$permLookup['create'] = $permModel->id;
					break;
				case "group_{$this->id}_edit_document":
					$permLookup['edit'] = $permModel->id;
					break;
				case "group_{$this->id}_delete_document":
					$permLookup['delete'] = $permModel->id;
					break;
				case "group_{$this->id}_manage_document":
					$permLookup['manage'] = $permModel->id;
					break;
			}
		}
		
		$ownerRole->perms()->sync($permIds);
		
		$editorRole = new Role();
		$editorRole->name = "group_{$this->id}_editor";
		$editorRole->save();
		
		$editorRole->perms()->sync(array(
			$permLookup['create'],
			$permLookup['edit'],
			$permLookup['manage']
		));
		
		$staffRole = new Role();
		$staffRole->name = "group_{$this->id}_staff";
		$staffRole->save();
		
		$users = array(
			static::ROLE_OWNER => $this->findUsersByRole(static::ROLE_OWNER),
			static::ROLE_EDITOR => $this->findUsersByRole(static::ROLE_EDITOR),
			static::ROLE_STAFF => $this->findUsersByRole(static::ROLE_STAFF)
		);
		
		
		foreach($users as $role => $userList) {
			foreach($userList as $userObj) {
				switch($role) {
					case static::ROLE_OWNER:
						$userObj->attachRole($ownerRole);
						break;
					case static::ROLE_EDITOR:
						$userObj->attachRole($editorRole);
						break;
					case static::ROLE_STAFF:
						$userObj->attachRole($staffRole);
						break;
				}
			}
		}
	}
	
	public function destroyRbacRules()
	{
		$permissions = $this->getPermissionsArray();
		
		$members = GroupMember::where('group_id', '=', $this->id)->get();
		
		$roles = Role::where('name', '=', "group_{$this->id}_owner")
					 ->orWhere('name', '=', "group_{$this->id}_editor")
					 ->orWhere('name', '=', "group_{$this->id}_staff")
					 ->get();
		
		var_dump($roles);
		
		foreach($roles as $role) {
			
			foreach($members as $member) {
				$user = User::where('id', '=', $member->user_id)->first();
				$user->detachRole($role);
			}
			if($role instanceof Role) {
				$role->delete();
			}
		}
		
		foreach($permissions as $permData) {
			$perm = Permission::where('name', '=', $permData['name'])->first();
			
			if($perm instanceof Permission) {
				$perm->delete();
			}
		}
	}
	
	public function members() 
	{
		return $this->hasMany('GroupMember');
	}
	
	static public function findByUserId($userId, $onlyActive = true)
	{
		$groupMember = static::join('group_members', 'groups.id', '=', 'group_members.group_id')
							 ->where('group_members.user_id', '=', $userId);
		
		if($onlyActive) {
			$groupMember->where('groups.status', '=', static::STATUS_ACTIVE);
		}
		
		return $groupMember->get(array(
			'groups.id', 'groups.name', 'groups.address1',
			'groups.address2', 'groups.city', 'groups.state',
			'groups.postal_code', 'groups.phone_number', 'groups.display_name',
			'groups.status', 'groups.created_at', 'groups.updated_at',
			'groups.deleted_at'));
	}
	
	static public function findByMemberId($memberId)
	{
		$groupMember = GroupMember::where('id', '=', $memberId)->first();
		
		if(!$groupMember) {
			return null;
		}
		
		return static::where('id', '=', $groupMember->group_id)->first();
	}
	
	static public function isValidUserForGroup($user_id, $group_id) 
	{
		$group = static::where('id', '=', $group_id)->first();
		
		if(!$group) {
			throw new Exception("Invalid Group ID $group_id");
			return false;
		}

		$member = $group->findMemberByUserId($user_id);
		
		if(!$member) {
			throw new Exception("Invalid Member ID");
			return false;
		}
		
		return true;
	}
	
	static public function findUsersByRole($role)
	{
		if(!static::isValidRole($role)) {
			return null;
		}
		
		$members = GroupMember::where('role', '=', $role)->get();
		
		$retval = new Collection();
		foreach($members as $member) {
			
			$userModel = User::where('id', '=', $member->user_id)->first();
			
			if($userModel) {
				$retval->add($userModel);
			}
		}
		
		return $retval;
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