<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sponsor as Requests;
use App\Models\Sponsor;
use App\Models\User;
use App\Events\SponsorCreated;
use Illuminate\Http\Request;
use Auth;
use Event;

class SponsorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Requests\Index $request)
    {
        $orderField = $request->input('order', 'updated_at');
        $orderDir = $request->input('order_dir', 'DESC');
        $limit = $request->input('limit', 10);

        $sponsorsQuery = Sponsor
            ::query();

        if ($request->has('name')) {
            $name = $request->get('name');
            $sponsorsQuery->where('name', 'LIKE', "%$name%");
        }

        // limit to selected members
        $userIds = $request->input('user_id', []);
        if (!empty($userIds)) {
            $sponsorsQuery->whereHas('members', function ($q) use ($userIds) {
                $q->whereIn('sponsor_members.user_id', $userIds);
            });
        }

        // if the user is logged in, lookup any sponsors they belong to so we
        // can widen the verification states we will allow
        $userSponsorIds = [];
        if ($request->user()) {
            if ($request->user()->isAdmin()) {
                // we'll just act like an admin is a member of every sponsor
                $userSponsorIds = Sponsor::select('id')->pluck('id')->toArray();
            } else {
                $userSponsorIds = $request->user()->sponsors()->pluck('sponsors.id')->toArray();
            }
        }

        // grab all the verification statuses we want to consider
        $requestedStatuses = $request->input('statuses', null);
        if (!$requestedStatuses) {
            // otherwise the are logged in and didn't specify any specific
            // statuses, so default to them all
            $requestedStatuses = Sponsor::getStatuses();
        }

        // limit the query to the verification statuses
        $sponsorsQuery->where(function ($sponsorsQuery) use ($requestedStatuses, $userSponsorIds) {
            foreach ($requestedStatuses as $status) {
                $sponsorsQuery->orWhere(function ($query) use ($status, $userSponsorIds) {
                    $query->where('status', $status);
                    switch($status) {
                        case Sponsor::STATUS_ACTIVE:
                            // nothing needed
                            break;
                        case Sponsor::STATUS_PENDING:
                            $query->whereIn('id', $userSponsorIds);
                            break;
                    }
                });
            }
        });

        // execute the query
        $sponsors = $sponsorsQuery
            ->orderby($orderField, $orderDir)
            ->paginate($limit);

        // easy list to expose or hide certain features in the UI
        $sponsorsCapabilities = [];
        $baseSponsorCapabilities = [
            'viewDocs' => true,
            'viewMembers' => true,
            'open' => true,
            'edit' => false,
            'viewStatus' => false,
            'editStatus' => false,
        ];
        $canSeeAtLeastOneStatus = false;

        foreach ($sponsors as $sponsor) {
            $caps = $baseSponsorCapabilities;
            if ($request->user()) {
                if ($request->user()->isAdmin()) {
                    $caps = array_map(function ($item) { return true; }, $caps);
                    $canSeeAtLeastOneStatus = true;
                } elseif ($sponsor->hasMember($request->user()->id)) {
                    $caps = array_map(function ($item) { return true; }, $caps);
                    $caps['editStatus'] = false;
                    $canSeeAtLeastOneStatus = true;
                }
            }
            $sponsorsCapabilities[$sponsor->id] = $caps;
        }

        $users = User::all();
        // only really needed if you are an admin, but doesn't hurt anything
        $validStatuses = Sponsor::getStatuses();

        return view('sponsors.list', compact([
            'sponsors',
            'sponsorsCapabilities',
            'canSeeAtLeastOneStatus',
            'users',
            'validStatuses',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Requests\Create $request)
    {
        return view('sponsors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\Store $request)
    {
        $sponsor = new Sponsor($request->all());
        $sponsor->status = Sponsor::STATUS_PENDING;

        if ($sponsor->save()) {
            $sponsor->addMember(Auth::user()->id, Sponsor::ROLE_OWNER);
            Event::fire(new SponsorCreated($sponsor));

            flash(trans('messages.sponsor.created'));
            return redirect()->route('sponsors.edit', $sponsor->id);
        } else {
            flash(trans('messages.sponsor.create_failed'));
            return back()->withInput();
        }
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
    public function edit(Requests\Edit $request, Sponsor $sponsor)
    {
        return view('sponsors.edit', compact('sponsor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Update $request, Sponsor $sponsor)
    {
        $sponsor->name = $request->input('name') ?: null;
        $sponsor->display_name = $request->input('display_name') ?: null;
        $sponsor->address1 = $request->input('address1') ?: null;
        $sponsor->address2 = $request->input('address2') ?: null;
        $sponsor->city = $request->input('city') ?: null;
        $sponsor->state = $request->input('state') ?: null;
        $sponsor->postal_code = $request->input('postal_code') ?: null;
        $sponsor->phone = $request->input('phone') ?: null;

        if ($sponsor->save()) {
            flash(trans('messages.sponsor.updated'));
            return redirect()->route('sponsors.edit', ['sponsor' => $sponsor->id]);
        } else {
            flash(trans('messages.sponsor.update_failed'));
            return redirect()->route('sponsors.edit', ['sponsor' => $sponsor->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateStatus(Requests\UpdateStatus $request, Sponsor $sponsor)
    {
        $sponsor->status = $request->input('status');
        $sponsor->save();

        flash(trans('messages.sponsor.status_updated'));
        return redirect()->route('sponsors.index', ['sponsor' => $sponsor->id]);
    }
}
