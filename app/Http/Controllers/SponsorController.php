<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sponsor as Requests;
use App\Models\Sponsor;
use App\Models\User;
use App\Events\SponsorCreated;
use Illuminate\Http\Request;
use Auth;

class SponsorController extends Controller
{

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
            return redirect()->route('sponsors.members.index', $sponsor->id);
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
        $documents = $sponsor->docs()->paginate($limit);

        return view('sponsors.documents-list', compact([
            'sponsor',
            'documents',
        ]));
    }
}
