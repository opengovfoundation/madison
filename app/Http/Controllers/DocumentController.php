<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Cache;
use Input;
use Response;
use Event;
use App\Http\Requests\UpdateDocumentRequest;
use File;
use Image;
use Storage;
use Redirect;
use App\Models\Setting;
use App\Models\User;
use App\Models\Group;
use App\Models\Doc;
use App\Models\DocAction;
use App\Models\DocMeta;
use App\Models\DocContent;
use App\Models\Annotation;
use App\Models\Comment;
use App\Models\Category;
use App\Models\MadisonEvent;
use \League\Csv\Writer;

/**
 * 	Controller for Document actions.
 */
class DocumentController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter('auth', array('on' => array('post', 'put', 'delete')));
    }

    public function getDoc($doc)
    {
        $doc_id = $doc;

        $doc = Doc::with('categories')->with('userSponsors')->with('groupSponsors')->find($doc);
        $doc->introtext = $doc->introtext()->first()['meta_value'];
        $doc->enableCounts();
        $doc->enableSponsors();

        // We have to manually json_encode this instead of using Response::json
        // because the encoding is inconsistent for integers between PHP
        // versions.  We use the JSON_NUMERIC_CHECK flag to normalize this.
        return Response::make(
            json_encode($doc->toArray(), JSON_NUMERIC_CHECK), 200);
    }

    public function getDocBySlug($slug)
    {
        $doc = Doc::findDocBySlug($slug);
        $introtext = $doc->introtext()->first()['meta_value'];
        $doc->introtext = $introtext;

        $doc->enableCounts();

        return Response::json($doc);
    }

    public function getEmbedded($slug = null)
    {
        $doc = Doc::findDocBySlug($slug);

        if (is_null($doc)) {
            App::abort('404');
        }

        $view = View::make('doc.reader.embed', compact('doc'));

        return $view;
    }

    /**
     * 	Post route for creating / updating documents.
     */
    public function postDocs()
    {
        $user = Auth::user();

        if (!$user->can('admin_manage_documents')) {
            // If there's a group
            if (Input::get('group_id')) {
                $groupUser = User::with('groups')->whereHas('groups', function ($query) {
                     $query->where('status', 'active');
                     $query->where('group_id', Input::get('group_id'));
                })->find($user->id);

                if (!isset($groupUser->id)) {
                    return Response::json($this->growlMessage("You do not have permission", 'error'));
                }
            } elseif (!$user->getSponsorStatus()) {
                return Response::json($this->growlMessage("You do not have permission", 'error'));
            }
        }

        //Creating new document
        $title = Input::get('title');
        $slug = Input::get('slug',
            str_replace(array(' ', '.'), array('-', ''), strtolower($title)));


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

            if (Input::get('group_id')) {
                $doc->groupSponsors()->sync(array(Input::get('group_id')));
            } else {
                $doc->userSponsors()->sync(array($user->id));
            }

            $starter = new DocContent();
            $starter->doc_id = $doc->id;
            $starter->content = "New Doc Content";
            $starter->save();

            $doc->init_section = $starter->id;
            $doc->save();

            $response = $this->growlMessage('Document created successfully', 'success');
            $response['doc'] = $doc->toArray();
            return Response::json($response, 200);
        } catch (Exception $e) {
            return Response::json($this->growlMessage($e->getMessage(), 'error'));
        }
    }

    public function update($id, UpdateDocumentRequest $request)
    {
        $doc = Doc::find($id);
        if (!$doc) return response('Not found.', 404);
        $doc->update($request->all());
        return Response::json($doc);
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

    public function postPublishState($id)
    {
        $doc = Doc::find($id);
        $doc->publish_state = Input::get('publish_state');
        $doc->save();

        $response['messages'][0] = array('text' => 'Document publish state saved', 'severity' => 'info');

        return Response::json($response);
    }

    public function postSlug($id)
    {
        $doc = Doc::find($id);
        // Compare current and new slug
        $old_slug = $doc->slug;
        // If the new slug is different, save it
        if ($old_slug != Input::get('slug')) {

            if (Doc::where('slug', Input::get('slug'))->count()) {
                $response['messages'][0] = array('text' => 'That slug is already taken, please choose another.', 'severity' => 'error');
            }
            else {
                $doc->slug = Input::get('slug');
                $doc->save();
                $response['messages'][0] = array('text' => 'Document slug saved', 'severity' => 'info');
            }
        } else {
            // If the slugs are identical, the only way this could have happened is if the sanitize
            // function took out an invalid character and tried to submit an identical slug

            // 20160229: This isn't behaving as expected, since it also happens
            // after fixing any other error. Commenting out for now. -BH
            // $response['messages'][0] = array('text' => 'Invalid slug character', 'severity' => 'error');
        }

        return Response::json($response);
    }

    public function getContent($id)
    {
        $page = Input::get('page', 1);
        if (!$page) {
            $page = 1;
        }

        $format = Input::get('format');
        if (!$format) {
            $format = 'html';
        }

        $cacheKey = 'doc-'.$id.'-'.$page.'-'.$format;

        if ($format === 'html' && Cache::has($cacheKey)) {
            return Response::json(Cache::get($cacheKey));
        }

        $docContent = DocContent::where('doc_id', $id)->
            limit(1)->offset($page - 1)->first();

        $returned = array();

        if ($docContent) {
            if($format === 'raw' || $format === 'all') {
                $returned['raw'] = $docContent->content;
            }
            if($format === 'html' || $format === 'all') {
                $returned['html'] = $docContent->html();
            }
        }

        if ($format === 'html') {
            Cache::forever($cacheKey, $returned);
            $returned['cached'] = false;
        }

        return Response::json($returned);
    }


    public function postContent($docId)
    {
        $doc = Doc::find($docId);

        if($doc) {
            $last_page = DocContent::where('doc_id', $docId)->max('page');
            if(!$last_page) {
                $last_page = 0;
            }

            $doc_content = new DocContent();
            $doc_content->content = Input::get('content', '');
            $doc_content->page = $last_page + 1;
            $doc->content()->save($doc_content);

            Event::fire(MadisonEvent::DOC_EDITED, $doc);

            return Response::json($doc_content->toArray());
        }
    }


    public function putContent($docId, $page)
    {
        $doc_content = DocContent::where('doc_id', $docId)
            ->where('page', $page)->first();

        if($doc_content) {

            $doc_content->content = Input::get('content', '');
            $doc_content->save();

            // Invalidate the cache
            $format = 'html';
            $cacheKey = 'doc-'.$docId.'-'.$page.'-'.$format;
            Cache::forget($cacheKey);

            Event::fire(MadisonEvent::DOC_EDITED, Doc::find($docId));

            return Response::json($doc_content->toArray());
        }
    }

    public function deleteContent($docId, $page)
    {
        $doc_content = DocContent::where('doc_id', $docId)
            ->where('page', $page)->first();
        if($doc_content) {
            $doc_content->delete();

            DocContent::where('doc_id', $docId)
                ->where('page', '>', $page)
                ->decrement('page');

            $doc = Doc::find($docId);
            $doc->enableCounts();

            Event::fire(MadisonEvent::DOC_EDITED, $doc);

            return Response::json($doc->toArray());
        }
    }

    public function deleteDoc($docId)
    {
        $admin_flag = Input::get('admin');
        $doc = Doc::find($docId);
        if (!$doc) return response('Not found.', 404);

        if ($admin_flag) {
            $doc->publish_state = Doc::PUBLISH_STATE_DELETED_ADMIN;
        } else {
            $doc->publish_state = Doc::PUBLISH_STATE_DELETED_USER;
        }

        $doc->save();

        $doc->comments()->delete();
        $doc->annotations()->delete();
        $doc->doc_meta()->delete();
        $doc->content()->delete();

        $result = $doc->delete();

        return Response::json($result);
    }

    public function getRestoreDoc($docId)
    {
        $doc = Doc::withTrashed()->find($docId);

        if ($doc->publish_state == Doc::PUBLISH_STATE_DELETED_ADMIN) {
            if (!Auth::user()->hasRole('admin')) {
                return Response('Unauthorized.', 403);
            }
        }

        if (!$doc->canUserEdit(Auth::user())) {
            return Response('Unauthorized.', 403);
        }

        DocMeta::withTrashed()->where('doc_id', $docId)->restore();
        DocContent::withTrashed()->where('doc_id', $docId)->restore();
        Annotation::withTrashed()->where('doc_id', $docId)->restore();
        Comment::withTrashed()->where('doc_id', $docId)->restore();

        $doc->restore();
        $doc->publish_state = Doc::PUBLISH_STATE_UNPUBLISHED;
        $doc->save();

        return Response::json($doc);
    }

    public function getDocs($state = null)
    {
        // Handle order by.
        $order_field = Input::get('order', 'updated_at');
        $order_dir = Input::get('order_dir', 'DESC');
        $discussion_state = Input::get('discussion_state', null);

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
                ->where('is_template', '!=', '1');

            if ($discussion_state) {
                $doc->where('discussion_state', '=', $discussion_state);
            }

            if (isset($state) && $state == 'all') {
                if (!Auth::check() || !Auth::user()->hasRole('Admin')) {
                    return response('Unauthorized.', 403);
                }
            } else {
                $doc->where('publish_state', '=', Doc::PUBLISH_STATE_PUBLISHED);
            }

            if (Input::has('category')) {
                $doc->whereHas('categories', function ($q) {
                    $category = Input::get('category');
                    $q->where('categories.name', 'LIKE', "%$category%");
                });
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

        return Response::json(Doc::prepareCountsAndDates($docs));
    }

    public function getDeletedDocs() {
        $admin_flag = Input::get('admin');
        $query = Doc::onlyTrashed()->with('sponsor')->where('is_template', '!=', '1');

        $publish_states = [Doc::PUBLISH_STATE_DELETED_USER];

        // If admin flag is passed, check auth and then add
        if ($admin_flag) {
            if (!Auth::user()->hasRole('admin')) {
                return Response('Unauthorized.', 403);
            }
            $publish_states[] = Doc::PUBLISH_STATE_DELETED_ADMIN;
        } else {
            $query->belongsToUser(Auth::user()->id);
        }

        $query->whereIn('publish_state', $publish_states);

        $docs = $query->get();

        return Response::json(Doc::prepareCountsAndDates($docs));
    }

    public function getDocCount() {
        $doc = Doc::where('publish_state', '=', Doc::PUBLISH_STATE_PUBLISHED)
            ->where('is_template', '!=', '1');

        if (Input::has('category')) {
            $doc->whereHas('categories', function ($q) {
                $category = Input::get('category');
                $q->where('categories.name', 'LIKE', "%$category%");
            });
        }

        if (Input::has('title')) {
            $title = Input::get('title');
            $doc->where('title', 'LIKE', "%$title%");
        }

        $docCount = $doc->count();

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
            $sponsor->sponsorType = str_replace('App\Models\\', '', get_class($sponsor));

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
                    $doc->userSponsors()->sync(array($user->id));
                    $doc->groupSponsors()->sync(array());
                    $response = $user->toArray();
                    break;
                case 'group':
                    $group = Group::find($sponsor['id']);
                    $doc->groupSponsors()->sync(array($group->id));
                    $doc->userSponsors()->sync(array());
                    $response = $group->toArray();
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
                case ($sponsor instanceof \App\Models\User):
                    $userSponsor = $sponsor->toArray();
                    $userSponsor['sponsorType'] = 'user';

                    $retval['sponsors'][] = $userSponsor;

                    break;
                case ($sponsor instanceof \App\Models\Group):

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

    /**
     *	Method to handle document RSS feeds.
     *
     *	@param string $slug
     *
     * @return view $feed->render()
     */
    public function getFeed($slug)
    {
        $doc = Doc::where('slug', $slug)->with('comments', 'annotations', 'userSponsors', 'groupSponsors')->first();

        $feed = Feed::make();

        $feed->title = $doc->title;
        $feed->description = "Activity feed for '".$doc->title."'";
        $feed->link = URL::to('docs/'.$slug);
        $feed->pubdate = $doc->updated_at;
        $feed->lang = 'en';

        $activities = $doc->comments->merge($doc->annotations);

        $activities = $activities->sort(function ($a, $b) {
            return (strtotime($a['updated_at']) > strtotime($b['updated_at'])) ? -1 : 1;
        });

        foreach ($activities as $activity) {
            $item = $activity->getFeedItem();

            array_push($feed->items, $item);
        }

        return $feed->render('atom');
    }

    /**
     *	Method to handle posting support/oppose clicks on a document.
     *
     * @param int $doc
     *
     * @return json array
     */
    public function postSupport($doc)
    {
        $input = Input::get();

        $supported = (bool) $input['support'];

        $docMeta = DocMeta::withTrashed()->where('user_id', Auth::user()->id)->where('meta_key', '=', 'support')->where('doc_id', '=', $doc)->first();

        if (!isset($docMeta)) {
            $docMeta = new DocMeta();
            $docMeta->doc_id = $doc;
            $docMeta->user_id = Auth::user()->id;
            $docMeta->meta_key = 'support';
            $docMeta->meta_value = (string) $supported;
            $docMeta->save();
        } elseif ($docMeta->meta_value == (string) $supported && !$docMeta->trashed()) {
            $docMeta->delete();
            $supported = null;
        } else {
            if ($docMeta->trashed()) {
                $docMeta->restore();
            }
            $docMeta->doc_id = $doc;
            $docMeta->user_id = Auth::user()->id;
            $docMeta->meta_key = 'support';
            $docMeta->meta_value = (string) (bool) $input['support'];
            $docMeta->save();
        }

        $supports = DocMeta::where('meta_key', '=', 'support')->where('meta_value', '=', '1')->where('doc_id', '=', $doc)->count();
        $opposes = DocMeta::where('meta_key', '=', 'support')->where('meta_value', '=', '')->where('doc_id', '=', $doc)->count();

        return Response::json(array('support' => $supported, 'supports' => $supports, 'opposes' => $opposes));
    }

    public function getFeatured()
    {
        $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

        if ($featuredSetting) {
            // Make sure our featured document can be viewed by the public.
            $featuredIds = explode(',', $featuredSetting->meta_value);
            $docQuery = Doc::with('categories')
                ->with('userSponsors')
                ->with('groupSponsors')
                ->with('statuses')
                ->with('dates')
                ->whereIn('id', $featuredIds)
                ->where('is_template', '!=', '1');

            if(Input::get('published') || (Auth::user() && !Auth::user()->hasRole('admin')))
            {
                $docQuery->where('publish_state', '=', Doc::PUBLISH_STATE_PUBLISHED);
            }

            $docs = $docQuery->get();

            if($docs) {
                // Reorder based on our previous list.
                $tempDocs = array();
                $orderList = array_flip($featuredIds);
                foreach($docs as $key=>$doc) {
                    $tempDocs[(int) $orderList[$doc->id]] = $doc;
                }

                // If you set the key of an array value as we do above,
                // PHP will internally store the object as an associative
                // array (hash), not as a list, and will return the elements
                // in the order assigned, not by the key order.
                // This means our attempt to re-order the object will fail.
                // The line below will restore the order. Ugh.
                ksort($tempDocs);
                $docs = $tempDocs;

            }
        }

        // If we don't have a document, just find anything recent.
        if(empty($docs) && !Input::get('featured_only')) {
            $docs = array(
                Doc::with('categories')
                ->with('userSponsors')
                ->with('groupSponsors')
                ->with('statuses')
                ->with('dates')
                ->where('publish_state', '=', Doc::PUBLISH_STATE_PUBLISHED)
                ->where('is_template', '!=', '1')
                ->orderBy('created_at', 'desc')
                ->first()
            );
        }

        // If we still don't have a document, give up.
        if (empty($docs)) {
            return Response::make(null, 404);
        }

        $return_docs = array();
        foreach($docs as $key => $doc) {
            $doc->enableCounts();
            $doc->enableSponsors();
            $return_doc = $doc->toArray();

            $return_doc['introtext'] = $doc->introtext()->first()['meta_value'];
            $return_doc['updated_at'] = date('c', strtotime($return_doc['updated_at']));
            $return_doc['created_at'] = date('c', strtotime($return_doc['created_at']));

            if(!$return_doc['thumbnail']) {
                $return_doc['thumbnail'] = '/img/default/default.jpg';
            }

            $return_docs[] = $return_doc;
        }

        return Response::json($return_docs);
    }

    // We just need a summary to respond to the post/put/delete methods.
    public function getFeaturedShort()
    {
        $docs = array();

        $featuredSetting = Setting::where(array('meta_key' => 'featured-doc'))->first();

        if ($featuredSetting) {
            // Make sure our featured document can be viewed by the public.
            $featuredIds = explode(',', $featuredSetting->meta_value);
            $docQuery = Doc::with('statuses')
                ->whereIn('id', $featuredIds)
                ->where('is_template', '!=', '1');
            $docs = $docQuery->get();

            if($docs) {
                // Reorder based on our previous list.
                $tempDocs = array();
                $orderList = array_flip($featuredIds);
                foreach($docs as $key=>$doc) {
                    $tempDocs[(int) $orderList[$doc->id]] = $doc;
                }

                // If you set the key of an array value as we do above,
                // PHP will internally store the object as an associative
                // array (hash), not as a list, and will return the elements
                // in the order assigned, not by the key order.
                // This means our attempt to re-order the object will fail.
                // The line below will restore the order. Ugh.
                ksort($tempDocs);
                $docs = $tempDocs;
            }
        }
        return Response::json($docs);
    }

    // Remove any docs that no longer exists.
    private function cleanDocs($docs)
    {
        $docs = array_filter($docs);
        if(is_array($docs) && count($docs)) {
            $existingDocs = array();

            $docResults = Doc::whereIn('id', $docs)
                ->where('is_template', '!=', '1')
                ->get();
            foreach($docResults as $doc) {
                $existingDocs[] = $doc->id;
            }

            $docs = array_values(array_intersect($docs, $existingDocs));
        }
        return $docs;
    }

    public function postFeatured()
    {
        if (!Auth::user()->hasRole('Admin')) {
            return Response::json($this->growlMessage('You are not authorized to change the Featured Document.', 'error'), 403);
        }

        $docId = Input::get('id');

        // firstOrNew() is not working for some reason, so we do it manually.
        $featuredSetting = Setting::where(array('meta_key' => 'featured-doc'))->first();
        if(!$featuredSetting)
        {
            $featuredSetting = new Setting;
            $featuredSetting->meta_key = 'featured-doc';
        }

        $docs = explode(',', $featuredSetting->meta_value);

        if(!in_array($docId, $docs)) {
            array_unshift($docs, $docId);
        }
        $featuredSetting->meta_value = join(',', $this->cleanDocs($docs));
        $featuredSetting->save();

        return $this->getFeaturedShort();
    }

    public function putFeatured()
    {
        if (!Auth::user()->hasRole('Admin')) {
            return Response::json($this->growlMessage('You are not authorized to change the Featured Document.', 'error'), 403);
        }

        $docs = explode(',', Input::get('docs'));

        // firstOrNew() is not working for some reason, so we do it manually.
        $featuredSetting = Setting::where(array('meta_key' => 'featured-doc'))->first();
        if(!$featuredSetting)
        {
            $featuredSetting = new Setting;
            $featuredSetting->meta_key = 'featured-doc';
        }

        $featuredSetting->meta_value = join(',', $this->cleanDocs($docs));
        $featuredSetting->save();

        return $this->getFeaturedShort();
    }

    public function deleteFeatured($docId)
    {
        if (!Auth::user()->hasRole('Admin')) {
            return Response::json($this->growlMessage('You are not authorized to change the Featured Document.', 'error'), 403);
        }

        // firstOrNew() is not working for some reason, so we do it manually.
        $featuredSetting = Setting::where(array('meta_key' => 'featured-doc'))->first();
        if(!$featuredSetting)
        {
            $featuredSetting = new Setting;
            $featuredSetting->meta_key = 'featured-doc';
        }

        $docs = explode(',', $featuredSetting->meta_value);

        if(in_array($docId, $docs)) {
            $docs = array_diff($docs, array($docId));
        }
        $featuredSetting->meta_value = join(',', $this->cleanDocs($docs));
        $featuredSetting->save();

        return $this->getFeaturedShort();
    }

    public function getImage($docId, $image)
    {
        $size = Input::get('size');

        $doc = Doc::where('id', $docId)->first();
        if($doc) {
            $path = $doc->getImagePath($image, $size);
            if(Storage::has($path)) {
                return response(Storage::get($path), 200)
                    ->header('Content-Type', Storage::mimeType($path));
            }
            else {
                return Response::make(null, 404);
            }
        }
        else {
            return Response::make(null, 404);
        }
    }

    public function uploadImage($docId)
    {
        if (Input::hasFile('file')) {
            $file = Input::file('file');

            try {
                $doc = Doc::where('id', $docId)->first();

                // Keep a record of our previous thumbnail.
                $previousThumbnail = $doc->thumbnail;

                $result = Storage::put($doc->getImagePath($file->getClientOriginalName()),
                    File::get($file));

                // Save the multiple sizes of this image.
                $sizes = config('madison.image_sizes');

                foreach($sizes as $name => $size)
                {
                    $img = Image::make($file);
                    if($size['crop'])
                    {
                        $img->fit($size['width'], $size['height']);
                    }
                    else {
                        $img->resize($size['width'], $size['height']);
                    }

                    Storage::put(
                        $doc->getImagePath($file->getClientOriginalName(), $size),
                        $img->stream()->__toString());

                    $result2 = $img->save();
                }

                // We want the featured image size to be the default.
                // Otherwise, we use the fullsize.
                $sizeName = null;
                if($sizes['featured'])
                {
                    $sizeName = 'featured';
                }

                $doc->thumbnail = $doc->getImageUrl($file->getClientOriginalName(),
                    $sizes[$sizeName]);
                $doc->save();

                // Our thumbnail was saved, so let's remove the old one.

                // Only do this if the name has changed, or we'll remove the
                // image we just uploaded.
                if($previousThumbnail !== $doc->thumbnail)
                {
                  // We just want the base name, not the resized one.
                  $imagePath = $doc->getImagePathFromUrl($previousThumbnail, true);

                  if(Storage::has($imagePath)) {
                    Storage::delete($imagePath);
                  }
                  foreach($sizes as $name => $size)
                  {
                    $imagePath = $doc->addSizeToImage($imagePath, $size);
                    if(Storage::has($imagePath)) {
                      Storage::delete($imagePath);
                    }
                  }
                }
            } catch (Exception $e) {
                return Response::json($this->growlMessage('There was an error with the image upload', 'error'), 500);
            }

            $params = [
                'imagePath' => $doc->thumbnail,
            ];

            return Response::json($this->growlMessage("Upload successful", 'success', $params));
        } else {
            return Response::json($this->growlMessage("There was an error uploading your image.", 'error'));
        }
    }

    public function deleteImage($docId)
    {
        $doc = Doc::where('id', $docId)->first();

        $image_path = $doc->getImagePathFromUrl($doc->thumbnail);

        if(Storage::has($image_path)) {
            try {
                Storage::delete($image_path);
            } catch (Exception $e) {
                Log::error("Error deleting document featured image for document id $docId");
                Log::error($e);
            }
        }
        $doc->thumbnail = null;
        $doc->save();
        return Response::json($this->growlMessage('Image deleted successfully', 'success'));
    }

    public function getUserDocuments()
    {
        $user = Auth::user();
        $docs = $user->docs->toArray();

        $groups = Auth::user()->groups;
        $groupDocs = [];

        foreach ($groups as $group) {
            $tempDocs = $group->docs()->get()->toArray();
            array_push($groupDocs, ['name' => $group->name, 'docs' => $tempDocs]);
        }

        $returned = [
            'independent'   => $docs,
            'group'         => $groupDocs,
        ];

        return Response::json($returned);
    }


    public function getActivity($docId)
    {
        $doc = Doc::where('id', $docId)->first();
        // We want to get the comments and annotations but not from our sponsor
        // or group.

        $skip_ids = array();
        foreach($doc->sponsor as $sponsor)
        {
            if($sponsor instanceof User)
            {
                $skip_ids[] = $sponsor->id;
            }
            elseif($sponsor instanceof Group)
            {
                foreach($sponsor->members as $member)
                {
                    $skip_ids[] = $member->user_id;
                }
            }
        }

        if(Input::get('summary') === 'general')
        {
            $statistics = array(
                'comments' => array(),
                'annotations' => array()
            );
            $statistics['comments']['total'] = $doc->comments()->
                whereNotIn('comments.user_id', $skip_ids)->
                count();
            $statistics['comments']['month'] = $doc->comments()->
                whereNotIn('comments.user_id', $skip_ids)->
                where('created_at', '>=',
                    \Carbon\Carbon::now()->subMonth()->toDateTimeString() )->
                count();
            $statistics['comments']['week'] = $doc->comments()->
                whereNotIn('comments.user_id', $skip_ids)->
                where('created_at', '>=',
                    \Carbon\Carbon::now()->subWeek()->toDateTimeString() )->
                count();
            $statistics['comments']['day'] = $doc->comments()->
                whereNotIn('comments.user_id', $skip_ids)->
                where('created_at', '>=',
                    \Carbon\Carbon::now()->subDay()->toDateTimeString() )->
                count();

            $statistics['annotations']['total'] = $doc->annotations()->
                whereNotIn('annotations.user_id', $skip_ids)->
                count();
            $statistics['annotations']['month'] = $doc->annotations()->
                whereNotIn('annotations.user_id', $skip_ids)->
                where('created_at', '>=',
                    \Carbon\Carbon::now()->subMonth()->toDateTimeString() )->
                count();
            $statistics['annotations']['week'] = $doc->annotations()->
                whereNotIn('annotations.user_id', $skip_ids)->
                where('created_at', '>=',
                    \Carbon\Carbon::now()->subWeek()->toDateTimeString() )->
                count();
            $statistics['annotations']['day'] = $doc->annotations()->
                whereNotIn('annotations.user_id', $skip_ids)->
                where('created_at', '>=',
                    \Carbon\Carbon::now()->subDay()->toDateTimeString() )->
                count();

            $statistics['annotations']['total'] += $doc->annotationComments()->
                whereNotIn('annotation_comments.user_id', $skip_ids)->
                count();

            $statistics['annotations']['month'] += $doc->annotationComments()->
                whereNotIn('annotation_comments.user_id', $skip_ids)->
                where('annotation_comments.created_at', '>=',
                    \Carbon\Carbon::now()->subMonth()->toDateTimeString() )->
                count();
            $statistics['annotations']['week'] += $doc->annotationComments()->
                whereNotIn('annotation_comments.user_id', $skip_ids)->
                where('annotation_comments.created_at', '>=',
                    \Carbon\Carbon::now()->subWeek()->toDateTimeString() )->
                count();
            $statistics['annotations']['day'] += $doc->annotationComments()->
                whereNotIn('annotation_comments.user_id', $skip_ids)->
                where('annotation_comments.created_at', '>=',
                    \Carbon\Carbon::now()->subDay()->toDateTimeString() )->
                count();

            return Response::json($statistics);
        }
        else
        {

        }
    }

    public function getSocialDoc($slug)
    {
        $doc = Doc::findDocBySlug($slug);
        if($doc) {
            $content = array(
                'title' => $doc->title,
                'description' => $doc->introtext()->first()['meta_value'],
                'image' => $doc->thumbnail
            );

            return view('layouts.social', $content);
        }
    }

    public function getActions($docId)
    {
        $actions = DocAction::where('doc_id', $docId)->with('user')->orderBy('created_at')->get();

        if($actions)
        {
            if(Input::get('download') === 'csv')
            {
                $csv = Writer::createFromFileObject(new \SplTempFileObject());

                $fields = array(
                    'first_name',
                    'last_name',
                    'email',
                    'quote',
                    'text',
                    'type',
                    'created_at'
                );
                // Headings.
                $csv->insertOne($fields);

                foreach($actions as $action)
                {
                    $actionRow = $action->toArray();
                    $actionRow['first_name'] = $actionRow['user']['fname'];
                    $actionRow['last_name'] = $actionRow['user']['lname'];
                    $actionRow['email'] = $actionRow['user']['email'];

                    // Rearrange our columns
                    $saveRow = array();
                    foreach($fields as $field)
                    {
                        $saveRow[$field] = $actionRow[$field];
                    }
                    $csv->insertOne($saveRow);
                }
                $csv->output('actions.csv');
            }
            else
            {
                return Response::json($actions->toArray());
            }
        }
        else
        {
            return Response::notFound();
        }
    }
}
