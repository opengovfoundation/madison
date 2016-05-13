<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;

/**
 * 	Controller for admin dashboard.
 */

class DashboardController extends Controller
{
    public $restful = true;

    public function __construct()
    {
        parent::__construct();

        //Filter to ensure user is signed in has an admin role
        $this->beforeFilter('admin');

        //Run csrf filter before all posts
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    /**
     * 	Dashboard Index View.
     */
    public function getIndex()
    {
        $data = array(
            'page_id'        => 'dashboard',
            'page_title'    => 'Dashboard',
        );

        return View::make('dashboard.index', $data);
    }

    public function getGroupverifications()
    {
        $user = Auth::user();

        if (!$user->can('admin_verify_users')) {
            return Redirect::to('/dashboard')->with('message', "You do not have permission");
        }

        $groups = Group::where('status', '!=', Group::STATUS_ACTIVE)->get();

        $data = array(
            'page_id' => 'verify_groups',
            'page_title' => 'Verify Groups',
            'requests' => $groups,
        );

        return View::make('dashboard.verify-group', $data);
    }

    public function getUserverifications()
    {
        $user = Auth::user();

        if (!$user->can('admin_verify_users')) {
            return Redirect::to('/dashboard')->with('message', "You do not have permission");
        }

        $users = UserMeta::where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_SPONSOR)
                         ->where('meta_value', '=', '0')
                         ->get();

        $userResults = new Collection();

        foreach ($users as $userMeta) {
            $userObj = $userMeta->user()->first();
            $userResults->add($userObj);
        }

        $data = array(
            'page_id' => 'verify_user_sponsor',
            'page_title' => 'Verify Independent Sponsors',
            'requests' => $userResults,
        );

        return View::make('dashboard.verify-independent', $data);
    }

    public function postNotifications(User $user)
    {
        if (Auth::user()->id !== $user->id) {
            return Response::json($this->growlMessage("You do not have permissions to edit this user's notification settings", "error"));
        }

        //Grab notifications array
        $notifications = Input::get('notifications');

        //Retrieve valid notification events
        $validNotifications = NotificationPreference::getValidNotifications();
        $events = array_keys($validNotifications);

        //Loop through each notification
        foreach ($notifications as $notification) {
            //Ensure this is a known event
            if (!in_array($notification['event'], $events)) {
                return Response::json($this->growlMessage('Unable to save settings.  Unknown event: '.$notification['event'], 'error'));
            }

            //Grab this notification from the database
            $model = NotificationPreference::where('user_id', '=', $user->id)->where('event', '=', $notification['event'])->first();

            //If we don't want that notification ( and it exists ), delete it
            if ($notification['selected'] === false) {
                if (isset($model)) {
                    $model->delete();
                }
            } else {
                //If the entry doesn't already exist, create it.
                    //Otherwise, ignore ( there was no change )
                if (!isset($model)) {
                    $model = new NotificationPreference();
                    $model->user_id = $user->id;
                    $model->event = $notification['event'];
                    $model->type = "email";

                    $model->save();
                }
            }
        }

        return Response::json($this->growlMessage('Settings saved successfully.', 'success'));
    }

    public function getNotifications()
    {
        $notifications = NotificationPreference::where('user_id', '=', Auth::user()->id)->get();
        $validNotifications = NotificationPreference::getValidNotifications();

        $selectedNotifications = [];
        foreach ($notifications as $n) {
            $selectedNotifications[] = $n->event;
        }

        return Response::json(compact('selectedNotifications', 'validNotifications'));
    }

    /**
     * 	Verification request view.
     */
    public function getVerifications()
    {
        $user = Auth::user();

        if (!$user->can('admin_verify_users')) {
            return Redirect::to('/dashboard')->with('message', "You do not have permission");
        }

        $requests = UserMeta::where('meta_key', 'verify')->with('user')->get();

        $data = array(
            'page_id'        => 'verify_users',
            'page_title'    => 'Verify Users',
            'requests'        => $requests,
        );

        return View::make('dashboard.verify-account', $data);
    }

    /**
     *	Settings page.
     */
    public function getSettings()
    {
        $data = array(
            'page_id'        => 'settings',
            'page_title'    => 'Settings',
        );

        $user = Auth::user();

        if (!$user->can('admin_manage_settings')) {
            return Redirect::to('/dashboard')->with('message', "You do not have permission");
        }

        return View::make('dashboard.settings', $data);
    }

    public function postSettings()
    {
        $user = Auth::user();

        if (!$user->can('admin_manage_settings')) {
            return Redirect::to('/dashboard')->with('message', "You do not have permission");
        }

        $adminEmail = Input::get('contact-email');

        $adminContact = User::where('email', '$adminEmail');

        if (!isset($adminContact)) {
            return Redirect::back()->with('error', 'The admin account with this email was not found.  Please try a different email.');
        }
    }

    /**
     * 	Document Creation/List or Document Edit Views.
     */
    public function getDocs($id = '')
    {
        $user = Auth::user();

        if (!$user->can('admin_manage_documents')) {
            return Redirect::to('/dashboard')->with('message', "You do not have permission");
        }

        if ($id == '') {
            $docs = Doc::all();

            $data = array(
                    'page_id'        => 'doc_list',
                    'page_title'    => 'Edit Documents',
                    'docs'            => $docs,
            );

            return View::make('dashboard.docs', $data);
        } else {
            $doc = Doc::find($id);
            if (isset($doc)) {
                $data = array(
                        'page_id'        => 'edit_doc',
                        'page_title'    => 'Edit '.$doc->title,
                        'doc'            => $doc,
                        // Just get the first content element.  We only have one, now.
                        'contentItem' => $doc->content()->where('parent_id')->first(),
                );

                return View::make('documents.edit', $data);
            } else {
                return Response::error('404');
            }
        }
    }

    /**
     * 	PUT route for saving documents.
     */
    public function putDocs($id = '')
    {
        $user = Auth::user();

        if (!$user->can('admin_manage_documents')) {
            return Redirect::to('/dashboard')->with('message', "You do not have permission");
        }

        $content = Input::get('content');
        $content_id = Input::get('content_id');

        if ($content_id) {
            try {
                $doc_content = DocContent::find($content_id);
            } catch (Exception $e) {
                return Redirect::to('dashboard/docs/'.$id)->with('error', 'Error saving the document: '.$e->getMessage());
            }
        } else {
            $doc_content = new DocContent();
        }

        $doc_content->doc_id = $id;
        $doc_content->content = $content;
        $doc_content->save();

        Event::fire(MadisonEvent::DOC_EDITED, $doc);

        $doc = Doc::find($id);
        $doc->indexContent($doc_content);

        return Redirect::to('dashboard/docs/'.$id)->with('success_message', 'Document Saved Successfully');
    }
}
