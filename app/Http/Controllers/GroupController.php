<?php

namespace App\Http\Controllers;

use Input;
use Response;
use Event;
use Auth;
use Session;
use Mail;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\MadisonEvent;

class GroupController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter('auth', array('on' => array('post', 'put', 'delete')));
    }

    public function getGroup($id = null)
    {
        $group = Group::find($id);

        return Response::json($group);
    }

    public function getRoles()
    {
        return Response::json(Group::getRoles());
    }

    public function postGroup($id = null)
    {
        if (Input::has('id')) {
            $group = Group::find(Input::get('id'));

            if (!$group->isGroupOwner(Auth::user()->id)) {
                return Response::json($this->growlMessage('You cannot modify a group you do not own.', 'error'));
            }

            $message = "Your group has been updated!";
        } else {
            $group = new Group();
            $group->status = Group::STATUS_PENDING;

            $message = "Your group has been created! It must be approved before you can invite others to join or create documents.";
        }

        $postData = array('name', 'display_name', 'address1', 'address2', 'city', 'state', 'postal_code', 'phone_number');

        foreach ($postData as $field) {
            $group->$field = Input::get($field);
        }

        if ($group->validate()) {
            $group->save();
            $group->addMember(Auth::user()->id, Group::ROLE_OWNER);

            if ($group->status === Group::STATUS_PENDING) {
                Event::fire(MadisonEvent::VERIFY_REQUEST_GROUP, $group);
            }
            return Response::json($this->growlMessage($message, 'success'));
        } else {
            return Response::json($this->growlMessage($group->getErrors()->all(), 'error'), 400);
        }
    }

    public function processMemberInvite($groupId)
    {
        $group = Group::where('id', '=', $groupId)->first();

        if (!$group) {
            return Response::json($this->growlMessage('Invalid Group ID', 'error'));
        }

        if (!$group->isGroupOwner(Auth::user()->id)) {
            return Response::json($this->growlMessage('You cannot add people to a group unless you are the group owner', 'error'));
        }

        $email = Input::all()['email'];
        $role = Input::all()['role'];

        if (!Group::isValidRole($role)) {
            return Response::json($this->growlMessage('Invalid role type.', 'error'));
        }

        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return Response::json($this->growlMessage('Invalid user', 'error'));
        }

        $userExists = (bool) GroupMember::where('user_id', '=', $user->id)
                                    ->where('group_id', '=', $group->id)
                                    ->count();

        if ($userExists) {
            return Response::json($this->growlMessage('This user is already a member of the group!', 'error'));
        }

        $newMember = new GroupMember();
        $newMember->user_id = $user->id;
        $newMember->group_id = $group->id;
        $newMember->role = $role;

        $newMember->save();
        $text = "You've been added to the group ".$group->getDisplayName()." with the role of ".$role.".";

        // Notify member of invite
        Mail::queue('email.notification', array('text' => $text), function ($message) use ($email) {
            $message->subject("You've been added to a Madison group");
            $message->from('sayhello@opengovfoundation.org', 'Madison');
            $message->to($email);
        });

        return Response::json($this->growlMessage('User added successfully', 'success'));
    }

    public function inviteMember($groupId)
    {
        $group = Group::where('id', '=', $groupId)->first();

        if (!$group) {
            return Redirect::back()->with('error', 'Invalid Group ID');
        }

        if (!$group->isGroupOwner(Auth::user()->id)) {
            return Redirect::back()->with('error', 'You cannot add people to a group unless you are the group owner');
        }

        if ($group->status != Group::STATUS_ACTIVE) {
            return Redirect::to('groups')->with('error', 'You cannot add people to an unverified group');
        }

        return View::make('groups.invite.index', compact('group'));
    }

    public function getMembers($groupId)
    {
        $groupMembers = GroupMember::findByGroupId($groupId);
        foreach ($groupMembers as $member) {
            $member->name = $member->getUserName();
        }

        return Response::json($groupMembers);
    }

    public function putMember($groupId, $memberId)
    {
        $role = Input::get('memberRole');

        $groupMember = GroupMember::where('id', $memberId)->first();

        $groupMember->role = $role;

        try {
            $groupMember->save();
        } catch (Exception $e) {
            return Response::json($this->growlMessage('There was an error updating the member role.', 'error'));
        }

        return Response::json($this->growlMessage('Member role updated successfully.', 'success'));
    }

    public function removeMember($groupId, $memberId)
    {
        $group = Group::find($groupId);

        if (!$group) {
            return Response::json($this->growlMessage("Group with id $groupId could not be found!", 'error'));
        }

        $members = GroupMember::where('group_id', '=', $group->id)->count();

        if ($members <= 1) {
            return Redirect::to('groups/members/'.(int) $group->id)->with('error', "You cannot remove the last member of the group");
        }

        $member = GroupMember::where('id', '=', $memberId);
        if (!$member) {
            return Response::json($this->growlMessage("Member with id $memberId does not exist.", 'error'));
        }

        $member->delete();

        return Response::json($this->growlMessage("Member removed successfully.", 'success'));
    }

    public function setActiveGroup($groupId)
    {
        try {
            if (!Auth::check()) {
                return Response::json($this->growlMessage('You must be logged in to use Madison as a group', 'error'), 401);
            }

            if ($groupId == 0) {
                Session::remove('activeGroupId');

                return Response::json($this->growlMessage('Active group has been removed', 'success'));
            }

            if (!Group::isValidUserForGroup(Auth::user()->id, $groupId)) {
                return Response::json($this->growlMessage('Invalid group', 'error'), 403);
            }

            Session::put('activeGroupId', $groupId);

            return Response::json($this->growlMessage('Active group changed', 'success'));
        } catch (\Exception $e) {
            Log::error($e);

            return Response::json($this->growlMessage('There was an error changing the active group', 'error'), 500);
        }
    }

    public function changeMemberRole($memberId)
    {
        $retval = array(
            'success' => false,
            'message' => "Unknown Error",
        );

        try {
            $groupMember = GroupMember::where('id', '=', $memberId)->first();

            if (!$groupMember) {
                $retval['message'] = "Could not locate member";

                return Response::json($retval);
            }

            $group = Group::where('id', '=', $groupMember->group_id)->first();

            if (!$group) {
                $retval['message'] = "Could not locate group";

                return Response::json($retval);
            }

            if (!$group->isGroupOwner(Auth::user()->id)) {
                $retval['message'] = "You aren't the group owner!";

                return Response::json($retval);
            }

            $newRole = Input::all('role')['role'];

            if (!Group::isValidRole($newRole)) {
                $retval['message'] = "Invalid Role: $newRole";

                return Response::json($retval);
            }

            if ($newRole != Group::ROLE_OWNER) {
                $owners = GroupMember::where('group_id', '=', $groupMember->group_id)
                                     ->where('role', '=', Group::ROLE_OWNER)
                                     ->count();

                if ($owners <= 1) {
                    $retval['message'] = "Group must have an owner!";

                    return Response::json($retval);
                }
            }

            $groupMember->role = $newRole;
            $groupMember->save();

            $retval['success'] = true;
            $retval['message'] = "Member Updated";

            return Response::json($retval);
        } catch (\Exception $e) {
            $retval['message'] = "Exception Caught: {$e->getMessage()}";

            return Response::json($retval);
        }

        return Response::json($retval);
    }

    public function getIndex()
    {
        if (!Auth::check()) {
            return Redirect::to('user/login')
                           ->with('error', 'Please log in to view groups');
        }
        $userGroups = GroupMember::where('user_id', '=', Auth::user()->id)->get();

        return View::make('groups.index', compact('userGroups'));
    }

    public function getEdit($groupId = null)
    {
        if (!Auth::check()) {
            return Redirect::to('user/login')
                            ->with('error', 'Please log in to edit a group');
        }

        if (is_null($groupId)) {
            $group = new Group();
        } else {
            $group = Group::where('id', '=', $groupId)->first();

            if (!$group) {
                return Redirect::back()->with('error', "Group Not Found");
            }

            if (!$group->isGroupOwner(Auth::user()->id)) {
                return Redirect::back()->with('error', 'You cannot edit the group you are not the owner');
            }
        }

        return View::make('groups.edit.index', compact('group'));
    }

    public function putEdit($groupId = null)
    {
        if (!Auth::check()) {
            return Redirect::to('user/login')
                        ->with('error', 'Please log in to edit a group');
        }

        $group_details = Input::all();

        $rules = array(
            'gname' => 'required',
        );

        $validation = Validator::make($group_details, $rules);

        if ($validation->fails()) {
            return Redirect::to('groups')->withInput()->withErrors($validation);
        }

        if (isset($group_details['groupId'])) {
            $groupId = $group_details['groupId'];
        }

        if (is_null($groupId)) {
            $group = new Group();
            $group->status = Group::STATUS_PENDING;

            $message = "Your group has been created! It must be approved before you can invite others to join or create documents.";
        } else {
            $group = Group::find($groupId);

            if (!$group->isGroupOwner(Auth::user()->id)) {
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

        if ($group->status == Group::STATUS_PENDING) {
            Event::fire(MadisonEvent::VERIFY_REQUEST_GROUP, $group);
        }

        return Redirect::to('groups')->with('success_message', $message);
    }

    public function getVerify()
    {
        $this->beforeFilter('admin');

        $status = Input::get('status');

        if ($status) {
            $groups = Group::where('status', '=', $status)->get();
        } else {
            $groups = Group::all();
        }

        return Response::json($groups);
    }

    public function putVerify($groupId)
    {
        $this->beforeFilter('admin');

        $newGroup = (object) Input::all();

        if (!Group::isValidStatus($newGroup->status)) {
            throw new \Exception("Invalid value for verify request");
        }

        $group = Group::where('id', '=', $groupId)->first();

        if (!$group) {
            throw new \Exception("Invalid Group");
        }

        $group->status = $newGroup->status;

        DB::transaction(function () use ($group) {
            $group->save();

            switch ($group->status) {
                case Group::STATUS_ACTIVE:
                    $group->createRbacRules();
                    break;
                case Group::STATUS_PENDING:
                    $group->destroyRbacRules();
                    break;
            }
        });

        return Response::json($group);
    }

}
