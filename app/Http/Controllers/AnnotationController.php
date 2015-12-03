<?php

namespace App\Http\Controllers;

/**
 * 	Controller for note actions.
 */
class AnnotationController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        //Require the user to be signed in to create, update notes
        $this->beforeFilter('auth', array('on' => array('post', 'put')));

        //Run CSRF filter before all POSTS
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    /**
     * 	GET note view.
     */
    public function getIndex($id = null)
    {
        //Return 404 if no id is passed
        if ($id == null) {
            App::abort(404, 'Note id not found');
        }

        //Invalid note id
        $annotation = Annotation::find($id);
        $user = $annotation->user()->first();

        if (!isset($annotation)) {
            App::abort(404, 'Unable to retrieve note');
        }

        //Retrieve note information

        $data = array(
            'page_id'            => 'Annotation',
            'page_title'    => 'View Annotation',
            'annotation'    => $annotation,
            'user'                => $user,
        );

        //Render view and return to user
        return View::make('annotation.index', $data);
    }
}
