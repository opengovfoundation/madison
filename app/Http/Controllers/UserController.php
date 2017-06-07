<?php

namespace App\Http\Controllers;

use App\Http\Requests\User as Requests;
use App\Mail\EmailVerification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Mail;

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

    public function editSettings(Requests\Edit $request, User $user)
    {
        return redirect()->route('users.settings.account.edit', $user->id);
    }

    public function editSettingsAccount(Requests\Edit $request, User $user)
    {
        return view('users.settings.account', compact('user'));
    }

    public function editSettingsPassword(Requests\Edit $request, User $user)
    {
        return view('users.settings.password', compact('user'));
    }

    public function editSettingsNotifications(Requests\Edit $request, User $user)
    {
        // Retrieve all valid notifications for user
        $validNotifications = NotificationPreference::getValidNotificationsForUser($user);

        // Retrieve all notification preferences that are set for user
        $currentNotifications = $user
            ->notificationPreferences()
            ->whereIn('event', array_keys($validNotifications))
            ->pluck('frequency', 'event')
            ;

        // Build array of notifications and their selected status
        $notificationPreferenceGroups = [];
        foreach ($validNotifications as $notificationName => $className) {
            if (!isset($notificationPreferenceGroups[$className::getType()])) {
                $notificationPreferenceGroups[$className::getType()] = [];
            }
            $value = isset($currentNotifications[$notificationName]) ? $currentNotifications[$notificationName] : null;
            $notificationPreferenceGroups[$className::getType()][$className] = $value;
        }

        $frequencyOptions = NotificationPreference::getValidFrequencies();

        return view('users.settings.notifications', compact('user', 'notificationPreferenceGroups', 'frequencyOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSettingsAccount(Requests\Settings\UpdateAccount $request, User $user)
    {
        $user->fname = $request->input('fname');
        $user->lname = $request->input('lname');
        $user->email = $request->input('email') ?: null;

        if ($user->save()) {
            flash(trans('messages.updated'));
            return back();
        } else {
            flash(trans('messages.update_failed'));
            return back()->withInput();
        }
    }

    public function updateSettingsPassword(Requests\Settings\UpdatePassword $request, User $user)
    {
        if ($request->input('new_password')) {
            $user->password = $request->input('new_password');
        }

        if ($user->save()) {
            flash(trans('messages.updated'));
        } else {
            flash(trans('messages.update_failed'));
        }

        return back();
    }

    public function updateSettingsNotifications(Requests\Settings\UpdateNotifications $request, User $user)
    {
        $validNotifications = array_keys(NotificationPreference::getValidNotificationsForUser($user));

        foreach ($validNotifications as $notificationName) {
            $notificationParamName = str_replace('.', '_', $notificationName);

            $newValue = $request->input($notificationParamName);
            if ($newValue === '') { $newValue = null; } // Turn empty strings to their proper null value

            // Grab this notification from the database
            $pref = $user
                ->notificationPreferences()
                ->where('event', $notificationName)
                ->first()
                ;

            if (isset($pref)) {
                $newValue ? $pref->update([ 'frequency' => $newValue ]) : $pref->delete();
            } else if ($newValue !== null) {
                $user->notificationPreferences()->create([
                    'event' => $notificationName,
                    'type' => NotificationPreference::TYPE_EMAIL,
                    'frequency' => $newValue,
                ]);
            }
        }

        flash(trans('messages.updated'));
        return back();
    }

    /**
     * List all sponsors for a particular user.
     *
     */
    public function sponsorsIndex(Requests\SponsorsIndex $request, User $user)
    {
        if ($request->user() && !$request->user()->isAdmin()
            && $request->user()->sponsors()->count() == 1
        ) {
            return redirect()->route('sponsors.documents.index', $request->user()->sponsors()->first());
        }

        $limit = $request->input('limit', 10);
        $sponsors = $user->sponsors()->paginate($limit);

        return view('sponsors.list', compact([
            'sponsors'
        ]));
    }


    public function verifyEmail(Requests\Edit $request, User $user, $token)
    {
        if (empty($user->token)) {
            flash(trans('messages.email_verification.already_verified'));
            return back();
        }

        if ($user->token === $token) {
            $user->token = '';
            $user->save();

            flash(trans('messages.email_verification.verified'));
            return back();
        }

        return back();
    }

    public function resendEmailVerification(Requests\Edit $request, User $user)
    {
        if (empty($user->token)) {
            flash(trans('messages.email_verification.already_verified'));
            return back();
        }

        Mail
            ::to($user)
            ->send(new EmailVerification($user));

        flash(trans('messages.email_verification.sent'));
        return back();
    }
}
