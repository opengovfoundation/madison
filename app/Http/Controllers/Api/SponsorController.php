<?php

namespace App\Http\Controllers\Api;

use Input;
use Response;
use Event;
use Auth;
use Session;
use Mail;
use DB;
use App\Models\User;
use App\Models\Sponsor;
use App\Models\SponsorMember;
use App\Http\Requests\Api\StoreSponsorRequest;
use App\Http\Requests\Api\UpdateSponsorRequest;
use App\Events\SponsorCreated;
use App\Events\SponsorMemberAdded;

class SponsorController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter('auth', array('on' => array('post', 'put', 'delete')));
    }

    /**
     * Store newly created Sponsor.
     *
     * @return Response
     */
    public function store(StoreSponsorRequest $request)
    {
        $sponsor = new Sponsor($request->all());
        $sponsor->status = Sponsor::STATUS_PENDING;
        $sponsor->save();
        $sponsor->addMember(Auth::user()->id, Sponsor::ROLE_OWNER);

        Event::fire(new SponsorCreated($sponsor));
        return response()->json($sponsor);
    }

    /**
     * Update a Sponsor.
     *
     * @return Response
     */
    public function update($id, UpdateSponsorRequest $request)
    {
        $sponsor = Sponsor::find($request->sponsor);
        if (!$sponsor) return response('Not found.', 404);
        $sponsor->update($request->all());
        return response()->json($sponsor);
    }

    public function getSponsor($id = null)
    {
        $sponsor = Sponsor::find($id);
        return Response::json($sponsor);
    }

    public function getRoles()
    {
        return Response::json(Sponsor::getRoles());
    }

    public function processMemberInvite($sponsorId)
    {
        $sponsor = Sponsor::where('id', '=', $sponsorId)->first();

        if (!$sponsor) {
            return Response::json($this->growlMessage('Invalid Sponsor ID', 'error'));
        }

        if (!$sponsor->isSponsorOwner(Auth::user()->id)) {
            return Response::json($this->growlMessage('You cannot add people to a sponsor unless you are the sponsor owner', 'error'));
        }

        $email = Input::all()['email'];
        $role = Input::all()['role'];

        if (!Sponsor::isValidRole($role)) {
            return Response::json($this->growlMessage('Invalid role type.', 'error'));
        }

        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return Response::json($this->growlMessage('Invalid user', 'error'));
        }

        $userExists = (bool) SponsorMember::where('user_id', '=', $user->id)
                                    ->where('sponsor_id', '=', $sponsor->id)
                                    ->count();

        if ($userExists) {
            return Response::json($this->growlMessage('This user is already a member of the sponsor!', 'error'));
        }

        $newMember = new SponsorMember();
        $newMember->user_id = $user->id;
        $newMember->sponsor_id = $sponsor->id;
        $newMember->role = $role;

        $newMember->save();

        Event::fire(new SponsorMemberAdded($newMember));

        return Response::json($this->growlMessage('User added successfully', 'success'));
    }

    public function inviteMember($sponsorId)
    {
        $sponsor = Sponsor::where('id', '=', $sponsorId)->first();

        if (!$sponsor) {
            return Redirect::back()->with('error', 'Invalid Sponsor ID');
        }

        if (!$sponsor->isSponsorOwner(Auth::user()->id)) {
            return Redirect::back()->with('error', 'You cannot add people to a sponsor unless you are the sponsor owner');
        }

        if ($sponsor->status != Sponsor::STATUS_ACTIVE) {
            return Redirect::to('sponsors')->with('error', 'You cannot add people to an unverified sponsor');
        }

        return View::make('sponsors.invite.index', compact('sponsor'));
    }

    public function getMembers($sponsorId)
    {
        $sponsorMembers = SponsorMember::findBySponsorId($sponsorId);
        foreach ($sponsorMembers as $member) {
            $member->name = $member->getUserName();
        }

        return Response::json($sponsorMembers);
    }

    public function putMember($sponsorId, $memberId)
    {
        $role = Input::get('memberRole');

        $sponsorMember = SponsorMember::where('id', $memberId)->first();

        $sponsorMember->role = $role;

        try {
            $sponsorMember->save();
        } catch (Exception $e) {
            return Response::json($this->growlMessage('There was an error updating the member role.', 'error'));
        }

        return Response::json($this->growlMessage('Member role updated successfully.', 'success'));
    }

    public function removeMember($sponsorId, $memberId)
    {
        $sponsor = Sponsor::find($sponsorId);

        if (!$sponsor) {
            return Response::json($this->growlMessage("Sponsor with id $sponsorId could not be found!", 'error'));
        }

        $members = SponsorMember::where('sponsor_id', '=', $sponsor->id)->count();

        if ($members <= 1) {
            return Redirect::to('sponsors/members/'.(int) $sponsor->id)->with('error', "You cannot remove the last member of the sponsor");
        }

        $member = SponsorMember::where('id', '=', $memberId);
        if (!$member) {
            return Response::json($this->growlMessage("Member with id $memberId does not exist.", 'error'));
        }

        $member->delete();

        return Response::json($this->growlMessage("Member removed successfully.", 'success'));
    }

    public function setActiveSponsor($sponsorId)
    {
        try {
            if (!Auth::check()) {
                return Response::json($this->growlMessage('You must be logged in to use Madison as a sponsor', 'error'), 401);
            }

            if ($sponsorId == 0) {
                Session::remove('activeSponsorId');

                return Response::json($this->growlMessage('Active sponsor has been removed', 'success'));
            }

            if (!Sponsor::isValidUserForSponsor(Auth::user()->id, $sponsorId)) {
                return Response::json($this->growlMessage('Invalid sponsor', 'error'), 403);
            }

            Session::put('activeSponsorId', $sponsorId);

            return Response::json($this->growlMessage('Active sponsor changed', 'success'));
        } catch (\Exception $e) {
            Log::error($e);

            return Response::json($this->growlMessage('There was an error changing the active sponsor', 'error'), 500);
        }
    }

    public function changeMemberRole($memberId)
    {
        $retval = array(
            'success' => false,
            'message' => "Unknown Error",
        );

        try {
            $sponsorMember = SponsorMember::where('id', '=', $memberId)->first();

            if (!$sponsorMember) {
                $retval['message'] = "Could not locate member";

                return Response::json($retval);
            }

            $sponsor = Sponsor::where('id', '=', $sponsorMember->sponsor_id)->first();

            if (!$sponsor) {
                $retval['message'] = "Could not locate sponsor";

                return Response::json($retval);
            }

            if (!$sponsor->isSponsorOwner(Auth::user()->id)) {
                $retval['message'] = "You aren't the sponsor owner!";

                return Response::json($retval);
            }

            $newRole = Input::all('role')['role'];

            if (!Sponsor::isValidRole($newRole)) {
                $retval['message'] = "Invalid Role: $newRole";

                return Response::json($retval);
            }

            if ($newRole != Sponsor::ROLE_OWNER) {
                $owners = SponsorMember::where('sponsor_id', '=', $sponsorMember->sponsor_id)
                                     ->where('role', '=', Sponsor::ROLE_OWNER)
                                     ->count();

                if ($owners <= 1) {
                    $retval['message'] = "Sponsor must have an owner!";

                    return Response::json($retval);
                }
            }

            $sponsorMember->role = $newRole;
            $sponsorMember->save();

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
                           ->with('error', 'Please log in to view sponsors');
        }
        $userSponsors = SponsorMember::where('user_id', '=', Auth::user()->id)->get();

        return View::make('sponsors.index', compact('userSponsors'));
    }

    public function getEdit($sponsorId = null)
    {
        if (!Auth::check()) {
            return Redirect::to('user/login')
                            ->with('error', 'Please log in to edit a sponsor');
        }

        if (is_null($sponsorId)) {
            $sponsor = new Sponsor();
        } else {
            $sponsor = Sponsor::where('id', '=', $sponsorId)->first();

            if (!$sponsor) {
                return Redirect::back()->with('error', "Sponsor Not Found");
            }

            if (!$sponsor->isSponsorOwner(Auth::user()->id)) {
                return Redirect::back()->with('error', 'You cannot edit the sponsor you are not the owner');
            }
        }

        return View::make('sponsors.edit.index', compact('sponsor'));
    }

    public function getVerify()
    {
        $this->beforeFilter('admin');

        $status = Input::get('status');

        if ($status) {
            $sponsors = Sponsor::where('status', '=', $status)->get();
        } else {
            $sponsors = Sponsor::all();
        }

        return Response::json($sponsors);
    }

    public function putVerify($sponsorId)
    {
        $this->beforeFilter('admin');

        $newSponsor = (object) Input::all();

        if (!Sponsor::isValidStatus($newSponsor->status)) {
            throw new \Exception("Invalid value for verify request");
        }

        $sponsor = Sponsor::where('id', '=', $sponsorId)->first();

        if (!$sponsor) {
            throw new \Exception("Invalid Sponsor");
        }

        $sponsor->status = $newSponsor->status;

        DB::transaction(function () use ($sponsor) {
            $sponsor->save();

            switch ($sponsor->status) {
                case Sponsor::STATUS_ACTIVE:
                    $sponsor->createRbacRules();
                    break;
                case Sponsor::STATUS_PENDING:
                    $sponsor->destroyRbacRules();
                    break;
            }
        });

        return Response::json($sponsor);
    }

}
