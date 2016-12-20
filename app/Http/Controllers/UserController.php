<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\User as Requests;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Requests\Edit $request, User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Update $request, User $user)
    {
        $user->fname = $request->input('fname');
        $user->lname = $request->input('lname');
        $user->email = $request->input('email') ?: null;
        $user->address1 = $request->input('address1') ?: null;
        $user->address2 = $request->input('address2') ?: null;
        $user->city = $request->input('city') ?: null;
        $user->state = $request->input('state') ?: null;
        $user->postal_code = $request->input('postal_code') ?: null;
        $user->phone = $request->input('phone') ?: null;
        $user->url = $request->input('url') ?: null;

        if ($request->input('new_password')) {
            $user->password = $request->input('new_password');
        }

        if ($user->save()) {
            flash(trans('messages.user.updated'));
            return redirect()->route('users.edit', ['user' => $user->id]);
        } else {
            flash(trans('messages.user.update_failed'));
            return redirect()->route('users.edit', ['user' => $user->id]);
        }
    }

}
