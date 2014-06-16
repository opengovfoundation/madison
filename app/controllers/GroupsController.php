<?php

class GroupsController extends Controller
{
	
	public function processMemberInvite($groupId)
	{
		$group = Group::where('id', '=', $groupId)->first();
		
		if(!$group) {
			return Redirect::back()->with('error', 'Invalid Group ID');
		}
		
		if(!$group->isGroupOwner(Auth::user()->id)) {
			return Redirect::back()->with('error', 'You cannot add people to a group unless you are the group owner');
		}
		
		$email = Input::all()['email'];
		$role = Input::all()['role'];
		
		if(!Group::isValidRole($role)) {
			return Redirect::back()->with('error', "Invalid Role Type");
		}
		
		$user = User::where('email', '=', $email)->first();
		
		if(!$user) {
			return Redirect::back()->with('error', "Invalid User");
		}
		
		$userExists = (bool)GroupMember::where('user_id', '=', $user->id)
									->where('group_id', '=', $group->id)
									->count();
		
		if($userExists) {
			return Redirect::back()->with('error', 'This user is already a member of the group!');
		}
		
		$newMember = new GroupMember();
		$newMember->user_id = $user->id;
		$newMember->group_id = $group->id;
		$newMember->role = $role;
		
		$newMember->save();
		
		return Redirect::to('groups/members/' . (int)$group->id)
						->with('success_message', 'User added successfully!');
	}
	
	public function inviteMember($groupId)
	{
		$group = Group::where('id', '=', $groupId)->first();
		
		if(!$group) {
			return Redirect::back()->with('error', 'Invalid Group ID');
		}
		
		if(!$group->isGroupOwner(Auth::user()->id)) {
			return Redirect::back()->with('error', 'You cannot add people to a group unless you are the group owner');
		}
		
		if($group->status != Group::STATUS_ACTIVE) {
			return Redirect::to('groups')->with('error', 'You cannot add people to an unverified group');
		}
		
		return View::make('groups.invite.index', compact('group'));
	}
	
	public function getMembers($groupId) 
	{
		$groupMembers = GroupMember::findByGroupId($groupId);
		$group = Group::where('id', '=', $groupId)->first();
		
		return View::make('groups.members.index', compact('groupMembers', 'group'));
	}
	
	public function removeMember($memberId)
	{
		$group = Group::findByMemberId($memberId);
		
		if(!$group) {
			return Redirect::to('groups')->with('error', "Could not locate a group this member belongs to");
		}
		
		$members = GroupMember::where('group_id', '=', $group->id)->count();
		
		if($members <= 1) {
			return Redirect::to('groups/members/' . (int)$group->id)->with('error', "You cannot remove the last member of the group");
		}
		
		$member = GroupMember::where('id', '=', $memberId);
		
		$member->delete();
		
		return Redirect::to('groups/members/' . (int)$group->id)->with('success_message', 'Member removed');
	}
	
	public function setActiveGroup($groupId)
	{
		try {
			
			if(!Auth::check()) {
				return Redirect::back()->with('error', 'You must be logged in to set a group');
			}
			
			if($groupId == 0) {
				Session::remove('activeGroupId');
				return Redirect::back()->with('message', 'Active Group has been removed');
			}
			
			if(!Group::isValidUserForGroup(Auth::user()->id, $groupId)) {
				return Redirect::back()->with('error', 'Invalid Group');
			}
			
			Session::put('activeGroupId', $groupId);
			
			return Redirect::back()->with('message', "Active Group Changed");
			
		} catch(\Exception $e) {
			return Redirect::back()->with('error', 'There was an error processing your request');
		}
	}
	
	public function changeMemberRole($memberId)
	{
		$retval = array(
			'success' => false,
			'message' => "Unknown Error"
		);
		
		try {
			
			$groupMember = GroupMember::where('id', '=', $memberId)->first();
			
			if(!$groupMember) {
				$retval['message'] = "Could not locate member";
				return Response::json($retval);
			}
			
			$group = Group::where('id', '=', $groupMember->group_id)->first();
			
			if(!$group) {
				$retval['message'] = "Could not locate group";
				return Response::json($retval);
			}
			
			if(!$group->isGroupOwner(Auth::user()->id)) {
				$retval['message'] = "You aren't the group owner!";
				return Response::json($retval);
			}
			
			$newRole = Input::all('role')['role'];
			
			if(!Group::isValidRole($newRole)) {
				$retval['message'] = "Invalid Role: $newRole";
				return Response::json($retval);
			}
			
			if($newRole != Group::ROLE_OWNER) {
				$owners = GroupMember::where('group_id', '=', $groupMember->group_id)
									 ->where('role', '=', Group::ROLE_OWNER)
									 ->count();
				
				if($owners <= 1) {
					$retval['message'] = "Group must have an owner!";
					return Response::json($retval);
				}
				
			}
			
			$groupMember->role = $newRole;
			$groupMember->save();
			
			$retval['success'] = true;
			$retval['message'] = "Member Updated";
			
			return Response::json($retval);
			
		} catch(\Exception $e) {
			$retval['message'] = "Exception Caught: {$e->getMessage()}";
			return Response::json($retval);
		}
		
		return Response::json($retval);
	}
	
	public function getIndex()
	{
		if(!Auth::check()) {
			return Redirect::to('user/login')
						   ->with('error', 'Please log in to view groups');
		}
		$userGroups = GroupMember::where('user_id', '=', Auth::user()->id)->get();
		return View::make('groups.index', compact('userGroups'));
	}
	
	public function getEdit($groupId = null)
	{
		if(!Auth::check()) {
			return Redirect::to('user/login')
							->with('error', 'Please log in to edit a group');
		}
		
		if(is_null($groupId)) {
			$group = new Group();
		} else {
			$group = Group::where('id','=',$groupId)->first();
			
			if(!$group) {
				return Redirect::back()->with('error', "Group Not Found");
			}
			
			if(!$group->isGroupOwner(Auth::user()->id)) {
				return Redirect::back()->with('error', 'You cannot edit the group you are not the owner');
			}
		}
		
		return View::make('groups.edit.index', compact('group'));
	}
	
	public function putEdit($groupId = null)
	{
		if(!Auth::check()) {
			return Redirect::to('user/login')
						->with('error', 'Please log in to edit a group');
		} 
		
		$group_details = Input::all();
		
		$rules = array(
			'gname'=> 'required',
		);
		
		$validation = Validator::make($group_details, $rules);
		
		if($validation->fails()) {
			return Redirect::to('groups')->withInput()->withErrors($validation);
		}
		
		if(isset($group_details['groupId'])) {
			$groupId = $group_details['groupId'];
		}
		
		if(is_null($groupId)) {
			$group = new Group();
			$group->status = Group::STATUS_PENDING;
			
			$message = "Your group has been created! It must be approved before you can invite others to join or create documents.";
			
		} else {
			$group = Group::find($groupId);
			
			if(!$group->isGroupOwner(Auth::user()->id)) {
				return Redirect::to('groups')->with('error', 'You cannot modify a group you do not own.');
			}
			$message = "Your group has been updated!";
		}
		
		
		$group->name = $group_details['gname'];
		$group->display_name = $group_details['dname'];
		$group->address1 = $group_details['address1'];
		$group->address2 = $group_details['address2'];
		$group->city = $group_details['city'];
		$group->state = $group_details['state'];
		$group->postal_code = $group_details['postal'];
		$group->phone_number = $group_details['phone'];
		
		$group->save();
		$group->addMember(Auth::user()->id, Group::ROLE_OWNER);

		return Redirect::to('groups')->with('success_message', $message);
		
	}
}