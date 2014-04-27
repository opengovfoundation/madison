<?php

class GroupsController extends Controller
{
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
		if(is_null($groupId)) {
			$group = new Group();
		} else {
			$group = Group::find($groupId)->first();
			
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
		
		if(is_null($groupId)) {
			$group = new Group();
			$group->status = Group::STATUS_PENDING;
			
			$message = "Your group has been created! It must be approved before you can invite others to join or create documents.";
			
		} else {
			$group = Group::find($groupId);
			// @todo make sure you can't edit a group you don't own
			$message = "Your group has been updated!";
		}
		
		$groupMember = new GroupMember();
		$groupMember->user_id = Auth::user()->id;
		$groupMember->role = Group::ROLE_OWNER;
		
		$group->name = $group_details['gname'];
		$group->address1 = $group_details['address1'];
		$group->address2 = $group_details['address2'];
		$group->city = $group_details['city'];
		$group->state = $group_details['state'];
		$group->postal_code = $group_details['postal'];
		$group->phone_number = $group_details['phone'];
		
		$group->save();
		
		$groupMember->group_id = $group->id;
		$groupMember->save();
		
		return Redirect::back()->with('success_message', $message);
		
	}
}