<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Http\Requests\SponsorMember as Requests;

class SponsorMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Sponsor $sponsor, Requests\Index $request)
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
