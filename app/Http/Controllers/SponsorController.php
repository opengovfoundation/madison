<?php

namespace App\Http\Controllers;

use App\Events\SponsorCreated;
use App\Http\Middleware\UnapprovedSponsorRedirect;
use App\Http\Requests\Sponsor as Requests;
use App\Models\Doc as Document;
use App\Models\Sponsor;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['info']);
        $this->middleware(UnapprovedSponsorRedirect::class)->except(['info', 'awaitingApproval']);
    }

    /**
     * Information page on becoming a sponsor.
     */
    public function info(Request $request)
    {
        return view('sponsors.info');
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
            event(new SponsorCreated($sponsor));

            flash(trans('messages.sponsor.created'));
            return redirect()->route('sponsors.awaiting-approval', $sponsor->id);
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
        return redirect()->route('sponsors.documents.index', $id);
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
     * Lists documents for a given sponsor.
     */
    public function documentsIndex(Requests\DocumentsIndex $request, Sponsor $sponsor)
    {
        $limit = $request->input('limit', 10);
        $showingDeleted = $request->input('deleted', false);
        $documentsQuery = $sponsor->docs();

        if ($showingDeleted) {
            $documentsQuery = $sponsor->docs()->onlyTrashed();

            if (!$request->user()->isAdmin()) {
                $documentsQuery->where('publish_state', '!=', Document::PUBLISH_STATE_DELETED_ADMIN);
            }
        }

        $documents = $documentsQuery->paginate($limit);

        return view('sponsors.documents-list', compact([
            'showingDeleted',
            'sponsor',
            'documents',
        ]));
    }

    public function awaitingApproval(Request $request)
    {
        return view('sponsors.awaiting-approval');
    }
}
