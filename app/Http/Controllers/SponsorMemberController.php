<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\SponsorMember;
use App\Models\User;
use App\Http\Requests\SponsorMember as Requests;
use App\Events\SponsorMemberAdded;
use App\Events\SponsorMemberRemoved;
use Event;

class SponsorMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Requests\Index $request, Sponsor $sponsor)
    {
        $orderField = $request->input('order', 'updated_at');
        $orderDir = $request->input('order_dir', 'DESC');
        $limit = $request->input('limit', 10);

        $allRoles = Sponsor::getRoles(true);
        $requestedRoles = $request->input('roles', null);

        if (!$requestedRoles) {
            $requestedRoles = Sponsor::getRoles();
        }

        $members = $sponsor->members()
            ->whereIn('role', $requestedRoles)
            ->orderby($orderField, $orderDir)
            ->paginate($limit);

        return view('sponsor_members.list', compact([
            'sponsor',
            'allRoles',
            'members',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Requests\Create $request, Sponsor $sponsor)
    {
        $allRoles = Sponsor::getRoles(true);

        return view('sponsor_members.create', compact([
            'sponsor',
            'allRoles',
        ]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\Store $request, Sponsor $sponsor)
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            flash(trans('messages.sponsor_member.failed_invalid_email'));
            return back()->withInput();
        }

        if ($sponsor->hasMember($user->id)) {
            flash(trans('messages.sponsor_member.failed_already_member'));
            return back()->withInput();
        }

        $newMember = $sponsor->addMember($user->id, $request->input('role', null));
        Event::fire(new SponsorMemberAdded($newMember, $request->user()));

        flash(trans('messages.sponsor_member.created'));
        return redirect()->route('sponsors.members.index', $sponsor->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Update the role for the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Models\Sponsor  $sponsor
     * @param  App\Models\SponsorMember $sponsorMember
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Requests\UpdateRole $request, Sponsor $sponsor, SponsorMember $member)
    {
        $ownerCount = $sponsor->members()->where('role', Sponsor::ROLE_OWNER)->count();

        if ($member->role == Sponsor::ROLE_OWNER && $ownerCount == 1) {
            flash(trans('messages.sponsor_member.need_owner'));
        } else {
            $member->role = $request->input('role');
            $member->save();

            flash(trans('messages.sponsor_member.role_updated'));
        }

        return redirect()->route('sponsors.members.index', $sponsor->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requests\Destroy $request, Sponsor $sponsor, SponsorMember $member)
    {
        $ownerCount = $sponsor->members()->where('role', Sponsor::ROLE_OWNER)->count();

        // if member being removed is an owner, make sure it's not the last owner
        if ($member->role == Sponsor::ROLE_OWNER && $ownerCount < 2) {
            flash(trans('messages.sponsor_member.need_owner'));
        } else {
            $user = $member->user;
            $member->delete();
            Event::fire(new SponsorMemberRemoved($sponsor, $user, $request->user()));
            flash(trans('messages.sponsor_member.removed'));
        }

        return redirect()->route('sponsors.members.index', $sponsor->id);
    }
}
