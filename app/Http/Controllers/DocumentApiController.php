<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Input;
use Response;
use Event;
use App\Models\Doc;
use App\Models\DocMeta;
use App\Models\DocContent;
use App\Models\Category;
use App\Models\MadisonEvent;

/**
 * 	Controller for Document actions.
 */
class DocumentApiController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter('auth', array('on' => array('post', 'put', 'delete')));
    }

    public function getDoc($doc)
    {
        $doc_id = $doc;

        $doc = Doc::with('content')->with('categories')->with('introtext')->where('is_template', '!=', '1')->find($doc);

        return Response::json($doc);
    }

    /**
     * 	Post route for creating / updating documents.
     */
    public function postDocs()
    {
        $user = Auth::user();

        if (!$user->can('admin_manage_documents')) {
            return Response::json($this->growlMessage("You do not have permission", 'error'));
        }

        //Creating new document
        $title = Input::get('title');
        $slug = str_replace(array(' ', '.'), array('-', ''), strtolower($title));

        // If the slug is taken
        if (Doc::where('slug', $slug)->count()) {
            $counter = 0;
            $tooMany = 10;
            do {
                if ($counter > $tooMany) {
                    return Response::json($this->growlMessage('Can\'t create document with that name, please try another.', 'error'));
                }
                $counter++;
                $new_slug = $slug . '-' . str_random(8);
            } while (Doc::where('slug', $new_slug)->count());

            $slug = $new_slug;
        }

        $doc_details = Input::all();

        $rules = array('title' => 'required');
        $validation = Validator::make($doc_details, $rules);
        if ($validation->fails()) {
            return Response::json($this->growlMessage('A valid title is required.', 'error'));
        }

        try {
            $doc = new Doc();
            $doc->title = $title;
            $doc->slug = $slug;
            $doc->save();
            $doc->sponsor()->sync(array($user->id));

            $starter = new DocContent();
            $starter->doc_id = $doc->id;
            $starter->content = "New Doc Content";
            $starter->save();

            $doc->init_section = $starter->id;
            $doc->save();

            $response = $this->growlMessage('Document created successfully', 'success');
            $response['doc'] = $doc->toArray();
            return Response::json($response);
        } catch (Exception $e) {
            return Response::json($this->growlMessage($e->getMessage(), 'error'));
        }
    }

    public function postTitle($id)
    {
        $rules = array('title' => 'required');
        $validation = Validator::make(Input::only('title'), $rules);
        if ($validation->fails()) {
            return Response::json($this->growlMessage('A valid title is required, changes are not saved', 'error'));
        }

        $doc = Doc::find($id);
        $doc->title = Input::get('title');
        $doc->save();

        $response['messages'][0] = array('text' => 'Document title saved', 'severity' => 'info');

        return Response::json($response);
    }

    public function postPrivate($id)
    {
        $doc = Doc::find($id);
        $doc->private = Input::get('private');
        $doc->save();

        $response['messages'][0] = array('text' => 'Document private saved', 'severity' => 'info');

        return Response::json($response);
    }

    public function postSlug($id)
    {
        $doc = Doc::find($id);
        // Compare current and new slug
        $old_slug = $doc->slug;
        // If the new slug is different, save it
        if ($old_slug != Input::get('slug')) {
            $doc->slug = Input::get('slug');
            $doc->save();
            $response['messages'][0] = array('text' => 'Document slug saved', 'severity' => 'info');
        } else {
            // If the slugs are identical, the only way this could have happened is if the sanitize
            // function took out an invalid character and tried to submit an identical slug
            $response['messages'][0] = array('text' => 'Invalid slug character', 'severity' => 'error');
        }

        return Response::json($response);
    }

    public function postContent($id)
    {
        $doc = Doc::find($id);
        $doc_content = DocContent::firstOrCreate(array('doc_id' => $doc->id));
        $doc_content->content = Input::get('content');
        $doc_content->save();
        $doc->content(array($doc_content));
        $doc->save();

        Event::fire(MadisonEvent::DOC_EDITED, $doc);

        $response['messages'][0] = array('text' => 'Document content saved', 'severity' => 'info');

        return Response::json($response);
    }

    public function getDocs()
    {
        // Handle order by.
        $order_field = Input::get('order', 'updated_at');
        $order_dir = Input::get('order_dir', 'DESC');

        // Handle pagination.
        $limit = null;
        $offset = null;
        $title = null;

        if (Input::has('limit')) {
            $limit = Input::get('limit');

            if (Input::has('page')) {
                $offset = (Input::get('page') - 1) * Input::get('limit');
            }
        }

        // Activity is a wholly different beast right now, requiring complicated
        // queries.
        if ($order_field === 'activity') {
            // TODO: Make this handle DESC order, maybe?
            $docs = Doc::getActive($limit, $offset);
        } else {
            $doc = Doc::getEager()->orderBy($order_field, $order_dir)
                ->where('private', '!=', '1')
                ->where('is_template', '!=', '1');

            if (Input::has('category')) {
                $doc = Doc::getEager()->whereHas('categories', function ($q) {
                    $category = Input::get('category');
                    $q->where('categories.name', 'LIKE', "%$category%");
                })
                    ->where('private', '!=', '1')
                    ->where('is_template', '!=', '1');
            }

            if (isset($limit)) {
                $doc->take($limit);
                if (isset($offset)) {
                    $doc->skip($offset);
                }
            }

            if (Input::has('title')) {
                $title = Input::get('title');
                $doc->where('title', 'LIKE', "%$title%");
            }



            $docs = $doc->get();
        }

        $return_docs = array();

        if ($docs) {
            foreach ($docs as $doc) {
                $doc->enableCounts();

                $return_doc = $doc->toArray();

                $return_doc['updated_at'] = date('c', strtotime($return_doc['updated_at']));
                $return_doc['created_at'] = date('c', strtotime($return_doc['created_at']));

                $return_docs[] = $return_doc;
            }
        }

        return Response::json($return_docs);
    }

    public function getDocCount() {
        $docs = Doc::where('private', '!=', '1')
            ->where('is_template', '!=', '1');

        $docCount = $docs->count();

        return Response::json([ 'count' => $docCount ]);
    }

    public function getCategories($doc = null)
    {
        if (!isset($doc)) {
            $categories = Category::all();
        } else {
            $doc = Doc::find($doc);
            $categories = $doc->categories()->get();
        }

        return Response::json($categories);
    }

    public function postCategories($doc)
    {
        $doc = Doc::find($doc);

        $categories = Input::get('categories');
        $categoryIds = array();

        foreach ($categories as $category) {
            $toAdd = Category::where('name', $category['text'])->first();

            if (!isset($toAdd)) {
                $toAdd = new Category();
            }

            $toAdd->name = $category['text'];
            $toAdd->save();

            array_push($categoryIds, $toAdd->id);
        }

        $doc->categories()->sync($categoryIds);
        $response['messages'][0] = array('text' => 'Categories saved', 'severity' => 'info');

        return Response::json($response);
    }

    public function getIntroText($doc)
    {
        $introText = DocMeta::where('meta_key', '=', 'intro-text')->where('doc_id', '=', $doc)->first();

        return Response::json($introText);
    }

    public function postIntroText($doc)
    {
        $introText = DocMeta::where('meta_key', '=', 'intro-text')->where('doc_id', '=', $doc)->first();

        if (!$introText) {
            $introText = new DocMeta();
            $introText->doc_id = $doc;
            $introText->meta_key = 'intro-text';
        }

        $text = Input::get('intro-text');
        $introText->meta_value = $text;

        $introText->save();

        $response['messages'][0] = array('text' => 'Intro Text Saved.', 'severity' => 'info');

        return Response::json($response);
    }

    public function hasSponsor($doc, $sponsor)
    {
        $result = Doc::find($doc)->sponsor()->find($sponsor);

        return Response::json($result);
    }

    public function getSponsor($doc)
    {
        $doc = Doc::find($doc);
        $sponsor = $doc->sponsor()->first();

        if ($sponsor) {
            $sponsor->sponsorType = get_class($sponsor);

            return Response::json($sponsor);
        }

        return Response::json();
    }

    public function postSponsor($doc)
    {
        $sponsor = Input::get('sponsor');

        $doc = Doc::find($doc);
        $response = null;

        if (!isset($sponsor)) {
            $doc->sponsor()->sync(array());
        } else {
            switch ($sponsor['type']) {
                case 'user':
                    $user = User::find($sponsor['id']);
                    $doc->userSponsor()->sync(array($user->id));
                    $doc->groupSponsor()->sync(array());
                    $response = $user;
                    break;
                case 'group':
                    $group = Group::find($sponsor['id']);
                    $doc->groupSponsor()->sync(array($group->id));
                    $doc->userSponsor()->sync(array());
                    $response = $group;
                    break;
                default:
                    throw new Exception('Unknown sponsor type '.$sponsor['type']);
            }
        }

        $response['messages'][0] = array('text' => 'Sponsor saved', 'severity' => 'info');

        return Response::json($response);
    }

    public function getStatus($doc)
    {
        $doc = Doc::find($doc);

        $status = $doc->statuses()->first();

        return Response::json($status);
    }

    public function postStatus($doc)
    {
        $toAdd = null;

        $status = Input::get('status');

        $doc = Doc::find($doc);

        if (!isset($status)) {
            $doc->statuses()->sync(array());
        } else {
            $toAdd = Status::where('label', $status['text'])->first();

            if (!isset($toAdd)) {
                $toAdd = new Status();
                $toAdd->label = $status['text'];
            }
            $toAdd->save();

            $doc->statuses()->sync(array($toAdd->id));
        }

        $response['messages'][0] = array('text' => 'Document saved', 'severity' => 'info');

        return Response::json($response);
    }

    public function getDates($doc)
    {
        $doc = Doc::find($doc);

        $dates = $doc->dates()->get();

        return Response::json($dates);
    }

    public function postDate($doc)
    {
        $doc = Doc::find($doc);

        $date = Input::get('date');

        $returned = new Date();
        $returned->label = $date['label'];
        $returned->date = date("Y-m-d H:i:s", strtotime($date['date']));

        $doc->dates()->save($returned);

        return Response::json($returned);
    }

    public function deleteDate($doc, $date)
    {
        $date = Date::find($date);

        if (!isset($date)) {
            throw new Exception("Unable to delete date.  Date id $date not found.");
        }

        $date->delete();

        return Response::json();
    }

    public function putDate($date)
    {
        $input = Input::get('date');
        $date = Date::find($date);

        if (!isset($date)) {
            throw new Exception("unable to update date.  Date id $date not found.");
        }

        $newDate = date("Y-m-d H:i:s", strtotime((string) $input['date']));

        $date->label = $input['label'];
        $date->date = $newDate;

        $date->save();

        $response['messages'][0] = array('text' => 'Document saved', 'severity' => 'info');

        return Response::json($response);
    }

    public function getAllSponsorsForUser()
    {
        $retval = array(
            'success' => false,
            'sponsors' => array(),
            'message' => "",
        );

        if (!Auth::check()) {
            $retval['message'] = "You must be logged in to perform this call";

            return Response::json($retval);
        }

        $sponsors = Auth::user()->getValidSponsors();

        foreach ($sponsors as $sponsor) {
            switch (true) {
                case ($sponsor instanceof User):
                    $userSponsor = $sponsor->toArray();
                    $userSponsor['sponsorType'] = 'user';

                    $retval['sponsors'][] = $userSponsor;

                    break;
                case ($sponsor instanceof Group):

                    $groupSponsor = $sponsor->toArray();
                    $groupSponsor['sponsorType'] = 'group';

                    $retval['sponsors'][] = $groupSponsor;
                    break;
                default:
                    break;
            }
        }

        $retval['success'] = true;

        return Response::json($retval);
    }

    public function getAllSponsors()
    {
        $doc = Doc::with('sponsor')->first();
        $sponsors = $doc->sponsor;

        return Response::json($sponsors);
    }

    public function getAllStatuses()
    {
        $doc = Doc::with('statuses')->first();

        $statuses = $doc->statuses;

        return Response::json($statuses);
    }
}
