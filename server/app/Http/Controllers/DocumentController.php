<?php

namespace App\Http\Controllers;

use App;
use Auth;
use Validator;
use Cache;
use Input;
use Response;
use Event;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\DocAccessEditRequest;
use App\Http\Requests\DocAccessReadRequest;
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
use App\Models\Role;
use Illuminate\Http\Request;
use URL;

/**
 * 	Controller for Document actions.
 */
class DocumentController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter('auth', ['on' => ['post', 'put', 'delete']]);
    }

    public function getDoc(DocAccessReadRequest $request, Doc $doc)
    {
        $doc->enableIntrotext();
        $doc->enableCounts();
        $doc->enableSponsors();

        // We have to manually json_encode this instead of using Response::json
        // because the encoding is inconsistent for integers between PHP
        // versions.  We use the JSON_NUMERIC_CHECK flag to normalize this.
        return Response::make(
            json_encode($doc->toArray(), JSON_NUMERIC_CHECK), 200
        )->header('content-type', 'application/json');
    }

    public function getDocBySlug(DocAccessReadRequest $request, Doc $doc)
    {
        $introtext = $doc->introtext()->first()['meta_value'];
        $doc->introtext = $introtext;

        $doc->enableCounts();

        return Response::json($doc);
    }

    public function getEmbedded(DocAccessEditRequest $request, Doc $doc)
    {
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
            } else {
                return Response::json($this->growlMessage("You do not have permission", 'error'));
            }
        }

        //Creating new document
        $title = Input::get('title');
        $slug = Input::get('slug', str_slug($title, '-'));


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

            $doc->sponsors()->sync([Input::get('group_id')]);

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

    public function update(UpdateDocumentRequest $request, Doc $doc)
    {
        $doc->update($request->all());
        $doc->setIntroText($request->input('introtext'));
        $doc->sponsors()->sync(array_pluck($request->input('sponsors'), 'id'));
        $doc->syncCategories($request->input('categories'));
        return Response::json($doc);
    }

    public function postTitle(DocAccessEditRequest $request, Doc $doc)
    {
        $rules = ['title' => 'required'];
        $validation = Validator::make(Input::only('title'), $rules);
        if ($validation->fails()) {
            return Response::json($this->growlMessage('A valid title is required, changes are not saved', 'error'));
        }

        $doc->title = Input::get('title');
        $doc->save();

        $response['messages'][0] = ['text' => 'Document title saved', 'severity' => 'info'];

        return Response::json($response);
    }

    public function postPublishState(DocAccessEditRequest $request, Doc $doc)
    {
        $doc->publish_state = Input::get('publish_state');
        $doc->save();

        $response['messages'][0] = ['text' => 'Document publish state saved', 'severity' => 'info'];

        return Response::json($response);
    }

    public function postSlug(DocAccessEditRequest $request, Doc $doc)
    {
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

    public function getContent(DocAccessReadRequest $request, Doc $doc)
    {
        $page = Input::get('page', 1);
        if (!$page) {
            $page = 1;
        }

        $format = Input::get('format');
        if (!$format) {
            $format = 'html';
        }

        $cacheKey = 'doc-'.$doc->id.'-'.$page.'-'.$format;

        if ($format === 'html' && Cache::has($cacheKey)) {
            return Response::json(Cache::get($cacheKey));
        }

        $docContent = DocContent
            ::where('doc_id', $doc->id)
            ->where('page', $page)
            ->limit(1)
            ->first()
            ;

        $returned = [];

        if ($docContent) {
            if ($format === 'raw' || $format === 'all') {
                $returned['raw'] = $docContent->content;
            }
            if ($format === 'html' || $format === 'all') {
                $returned['html'] = $docContent->html();
            }
        }

        if ($format === 'html') {
            Cache::forever($cacheKey, $returned);
            $returned['cached'] = false;
        }

        return Response::json($returned);
    }


    public function postContent(DocAccessEditRequest $request, Doc $doc)
    {
        $last_page = DocContent::where('doc_id', $doc->id)->max('page');
        if (!$last_page) {
            $last_page = 0;
        }

        $doc_content = new DocContent();
        $doc_content->content = Input::get('content', '');
        $doc_content->page = $last_page + 1;
        $doc->content()->save($doc_content);

        return Response::json($doc_content->toArray());
    }


    public function putContent(DocAccessEditRequest $request, Doc $doc, $page)
    {
        $doc_content = DocContent
            ::where('doc_id', $doc->id)
            ->where('page', $page)
            ->first()
            ;

        if ($doc_content) {

            $doc_content->content = Input::get('content', '');
            $doc_content->save();

            // Invalidate the cache
            $format = 'html';
            $cacheKey = 'doc-'.$doc->id.'-'.$page.'-'.$format;
            Cache::forget($cacheKey);

            return Response::json($doc_content->toArray());
        }
    }

    public function deleteContent(DocAccessEditRequest $request, Doc $doc, $page)
    {
        $doc_content = DocContent::where('doc_id', $doc->id)
            ->where('page', $page)->first();
        if ($doc_content) {
            $doc_content->delete();

            DocContent
                ::where('doc_id', $doc->id)
                ->where('page', '>', $page)
                ->decrement('page');

            $doc->enableCounts();

            return Response::json($doc->toArray());
        }
    }

    public function deleteDoc(DocAccessEditRequest $request, Doc $doc)
    {
        $admin_flag = Input::get('admin');

        if ($admin_flag) {
            $doc->publish_state = Doc::PUBLISH_STATE_DELETED_ADMIN;
        } else {
            $doc->publish_state = Doc::PUBLISH_STATE_DELETED_USER;
        }

        $doc->save();

        $doc->annotations()->delete();
        $doc->doc_meta()->delete();
        $doc->content()->delete();

        $result = $doc->delete();

        return Response::json($result);
    }

    public function getRestoreDoc(DocAccessEditRequest $request, Doc $doc)
    {
        if ($doc->publish_state == Doc::PUBLISH_STATE_DELETED_ADMIN) {
            if (!Auth::user()->hasRole(Role::ROLE_ADMIN)) {
                return Response('Unauthorized.', 403);
            }
        }

        DocMeta::withTrashed()->where('doc_id', $doc->id)->restore();
        DocContent::withTrashed()->where('doc_id', $doc->id)->restore();
        // TODO: does this work?
        $doc->annotations()->withTrashed()->restore();

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
        $query = Doc::onlyTrashed()->with('sponsors')->where('is_template', '!=', '1');

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

    public function getAllCategories(DocAccessReadRequest $request)
    {
        return Response::json(Category::all());
    }

    public function getDocCategories(DocAccessReadRequest $request, Doc $doc)
    {
        $categories = $doc->categories()->get();

        return Response::json($categories);
    }

    public function postCategories(DocAccessEditRequest $request, $doc)
    {
        $doc = Doc::find($doc);

        $categories = Input::get('categories');
        $categoryIds = [];

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
        $response['messages'][0] = ['text' => 'Categories saved', 'severity' => 'info'];

        return Response::json($response);
    }

    public function getIntroText(DocAccessReadRequest $request, Doc $doc)
    {
        $introText = DocMeta::where('meta_key', '=', 'intro-text')->where('doc_id', '=', $doc->id)->first();

        return Response::json($introText);
    }

    public function postIntroText(DocAccessEditRequest $request, Doc $doc)
    {
        $doc->setIntroText($request->get('intro-text'));

        $response['messages'][0] = ['text' => 'Intro Text Saved.', 'severity' => 'info'];

        return Response::json($response);
    }

    public function hasSponsor(DocAccessReadRequest $request, Doc $doc, $sponsor)
    {
        $result = $doc->sponsors()->find($sponsor);

        return Response::json($result);
    }

    public function getSponsor(DocAccessReadRequest $request, Doc $doc)
    {
        $sponsor = $doc->sponsors()->first();

        if ($sponsor) {
            return Response::json($sponsor);
        }

        return Response::json();
    }

    public function postSponsor(DocAccessEditRequest $request, Doc $doc)
    {
        $sponsor = Input::get('sponsor');

        $response = null;

        if (!isset($sponsor)) {
            throw new Exception('Must provide a sponsor');
        } else {
            $doc->sponsors()->sync([$sponsor['id']]);
        }

        $response['messages'][0] = ['text' => 'Sponsor saved', 'severity' => 'info'];

        return Response::json($response);
    }

    public function getStatus(DocAccessReadRequest $request, Doc $doc)
    {
        $status = $doc->statuses()->first();

        return Response::json($status);
    }

    public function postStatus(DocAccessEditRequest $request, Doc $doc)
    {
        $toAdd = null;

        $status = Input::get('status');

        if (!isset($status)) {
            $doc->statuses()->sync([]);
        } else {
            $toAdd = Status::where('label', $status['text'])->first();

            if (!isset($toAdd)) {
                $toAdd = new Status();
                $toAdd->label = $status['text'];
            }
            $toAdd->save();

            $doc->statuses()->sync([$toAdd->id]);
        }

        $response['messages'][0] = ['text' => 'Document saved', 'severity' => 'info'];

        return Response::json($response);
    }

    public function getDates(DocAccessReadRequest $request, Doc $doc)
    {
        $dates = $doc->dates()->get();

        return Response::json($dates);
    }

    public function postDate(DocAccessEditRequest $request, Doc $doc)
    {
        $date = Input::get('date');

        $returned = new Date();
        $returned->label = $date['label'];
        $returned->date = date("Y-m-d H:i:s", strtotime($date['date']));

        $doc->dates()->save($returned);

        return Response::json($returned);
    }

    public function deleteDate(DocAccessEditRequest $request, Doc $doc, $date)
    {
        $date = Date::find($date);

        if (!isset($date)) {
            throw new Exception("Unable to delete date. Date id $date not found.");
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

        $response['messages'][0] = ['text' => 'Document saved', 'severity' => 'info'];

        return Response::json($response);
    }

    public function getAllSponsorsForUser()
    {
        $retval = [
            'success' => false,
            'sponsors' => [],
            'message' => "",
        ];

        if (!Auth::check()) {
            $retval['message'] = "You must be logged in to perform this call";

            return Response::json($retval);
        }

        $sponsors = Auth::user()->getValidSponsors();

        foreach ($sponsors as $sponsor) {
            $retval['sponsors'][] = $sponsor->toArray();
        }

        $retval['success'] = true;

        return Response::json($retval);
    }

    public function getAllSponsors()
    {
        $doc = Doc::with('sponsors')->first();
        $sponsors = $doc->sponsors;

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
    public function getActivityFeed(DocAccessReadRequest $request, Doc $doc)
    {
        $feed = App::make('feed');

        $feed->title = $doc->title;
        $feed->description = "Activity feed for '".$doc->title."'";
        $feed->link = $doc->url;
        $feed->pubdate = $doc->updated_at;
        $feed->lang = 'en';

        $activities = $doc->comments()->orderBy('updated_at', 'DESC')->get();

        foreach ($activities as $activity) {
            $item = $activity->getFeedItem();

            $feed->addItem($item);
        }

        return $feed->render('atom');
    }

    public function getFeed()
    {
        //Grab all documents
        $docs = Doc
            ::with('sponsors', 'content')
            ->latest('updated_at')
            ->take(20)
            ->get();

        $feed = App::make('feed');

        $feed->title = 'Madison Documents';
        $feed->description = 'Latest 20 documents in Madison';
        $feed->link = URL::to('rss');
        $feed->pubdate = $docs->first()->updated_at;
        $feed->lang = 'en';

        foreach ($docs as $doc) {
            $item = [];
            $item['title'] = $doc->title;
            $item['author'] = $doc->sponsors->first()->display_name;
            $item['link'] = $doc->url;
            $item['pubdate'] = $doc->updated_at;
            $item['description'] = $doc->title;
            $item['content'] = $doc->fullContentHtml();
            $item['enclosure'] = [];
            $item['category'] = '';

            $feed->addItem($item);
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
    public function postSupport(DocAccessReadRequest $request, Doc $doc)
    {
        $input = Input::get();

        $supported = (bool) $input['support'];

        $docMeta = DocMeta::withTrashed()->where('user_id', Auth::user()->id)->where('meta_key', '=', 'support')->where('doc_id', '=', $doc->id)->first();

        if (!isset($docMeta)) {
            $docMeta = new DocMeta();
            $docMeta->doc_id = $doc->id;
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
            $docMeta->doc_id = $doc->id;
            $docMeta->user_id = Auth::user()->id;
            $docMeta->meta_key = 'support';
            $docMeta->meta_value = (string) (bool) $input['support'];
            $docMeta->save();
        }

        $supports = DocMeta::where('meta_key', '=', 'support')->where('meta_value', '=', '1')->where('doc_id', '=', $doc->id)->count();
        $opposes = DocMeta::where('meta_key', '=', 'support')->where('meta_value', '=', '')->where('doc_id', '=', $doc->id)->count();

        return Response::json(['support' => $supported, 'supports' => $supports, 'opposes' => $opposes]);
    }

    public function getFeatured()
    {
        $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

        if ($featuredSetting) {
            // Make sure our featured document can be viewed by the public.
            $featuredIds = explode(',', $featuredSetting->meta_value);
            $docQuery = Doc::with('categories')
                ->with('sponsors')
                ->with('statuses')
                ->with('dates')
                ->whereIn('id', $featuredIds)
                ->where('is_template', '!=', '1');

            if (Input::get('published') || (Auth::user() && !Auth::user()->hasRole('admin'))) {
                $docQuery->where('publish_state', '=', Doc::PUBLISH_STATE_PUBLISHED);
            }

            $docs = $docQuery->get();

            if ($docs) {
                // Reorder based on our previous list.
                $tempDocs = [];
                $orderList = array_flip($featuredIds);
                foreach ($docs as $key=>$doc) {
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
        if (empty($docs) && !Input::get('featured_only')) {
            $docs = [
                Doc::with('categories')
                ->with('sponsors')
                ->with('statuses')
                ->with('dates')
                ->where('publish_state', '=', Doc::PUBLISH_STATE_PUBLISHED)
                ->where('is_template', '!=', '1')
                ->orderBy('created_at', 'desc')
                ->first()
            ];
        }

        // If we still don't have a document, give up.
        if (empty($docs)) {
            return Response::make(null, 404);
        }

        $return_docs = [];
        foreach ($docs as $key => $doc) {
            $doc->enableCounts();
            $doc->enableSponsors();
            $return_doc = $doc->toArray();

            $return_doc['introtext'] = $doc->introtext()->first()['meta_value'];
            $return_doc['updated_at'] = date('c', strtotime($return_doc['updated_at']));
            $return_doc['created_at'] = date('c', strtotime($return_doc['created_at']));

            if (!$return_doc['thumbnail']) {
                $return_doc['thumbnail'] = '/img/default/default.jpg';
            }

            $return_docs[] = $return_doc;
        }

        return Response::json($return_docs);
    }

    // We just need a summary to respond to the post/put/delete methods.
    public function getFeaturedShort()
    {
        $docs = [];

        $featuredSetting = Setting::where(['meta_key' => 'featured-doc'])->first();

        if ($featuredSetting) {
            // Make sure our featured document can be viewed by the public.
            $featuredIds = explode(',', $featuredSetting->meta_value);
            $docQuery = Doc
                ::with('statuses')
                ->whereIn('id', $featuredIds)
                ->where('is_template', '!=', '1');
            $docs = $docQuery->get();

            if ($docs) {
                // Reorder based on our previous list.
                $tempDocs = [];
                $orderList = array_flip($featuredIds);
                foreach ($docs as $key=>$doc) {
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
        if (is_array($docs) && count($docs)) {
            $existingDocs = [];

            $docResults = Doc
                ::whereIn('id', $docs)
                ->where('is_template', '!=', '1')
                ->get();
            foreach ($docResults as $doc) {
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
        $featuredSetting = Setting::where(['meta_key' => 'featured-doc'])->first();
        if (!$featuredSetting)
        {
            $featuredSetting = new Setting;
            $featuredSetting->meta_key = 'featured-doc';
        }

        $docs = explode(',', $featuredSetting->meta_value);

        if (!in_array($docId, $docs)) {
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
        $featuredSetting = Setting::where(['meta_key' => 'featured-doc'])->first();
        if (!$featuredSetting) {
            $featuredSetting = new Setting;
            $featuredSetting->meta_key = 'featured-doc';
        }

        $featuredSetting->meta_value = join(',', $this->cleanDocs($docs));
        $featuredSetting->save();

        return $this->getFeaturedShort();
    }

    public function deleteFeatured(AdminRequest $request, Doc $doc)
    {
        // firstOrNew() is not working for some reason, so we do it manually.
        $featuredSetting = Setting::where(['meta_key' => 'featured-doc'])->first();
        if (!$featuredSetting)
        {
            $featuredSetting = new Setting;
            $featuredSetting->meta_key = 'featured-doc';
        }

        $docs = explode(',', $featuredSetting->meta_value);

        if (in_array($doc->id, $docs)) {
            $docs = array_diff($docs, [$doc->id]);
        }
        $featuredSetting->meta_value = join(',', $this->cleanDocs($docs));
        $featuredSetting->save();

        return $this->getFeaturedShort();
    }

    public function getImage(DocAccessReadRequest $request, Doc $doc, $image)
    {
        $size = Input::get('size');

        $path = $doc->getImagePath($image, $size);
        if (Storage::has($path)) {
            return response(Storage::get($path), 200)
                ->header('Content-Type', Storage::mimeType($path));
        } else {
            return Response::make(null, 404);
        }
    }

    public function uploadImage(DocAccessEditRequest $request, Doc $doc)
    {
        if (Input::hasFile('file')) {
            $file = Input::file('file');

            try {
                // Keep a record of our previous thumbnail.
                $previousThumbnail = $doc->thumbnail;

                $result = Storage::put(
                    $doc->getImagePath($file->getClientOriginalName()),
                    File::get($file)
                );

                // Save the multiple sizes of this image.
                $sizes = config('madison.image_sizes');

                foreach ($sizes as $name => $size)
                {
                    $img = Image::make($file);
                    if ($size['crop']) {
                        $img->fit($size['width'], $size['height']);
                    } else {
                        $img->resize($size['width'], $size['height']);
                    }

                    Storage::put(
                        $doc->getImagePath($file->getClientOriginalName(), $size),
                        $img->stream()->__toString()
                    );

                    $result2 = $img->save();
                }

                // We want the featured image size to be the default.
                // Otherwise, we use the fullsize.
                $sizeName = null;
                if ($sizes['featured']) {
                    $sizeName = 'featured';
                }

                $doc->thumbnail = $doc->getImageUrl(
                    $file->getClientOriginalName(),
                    $sizes[$sizeName]
                );
                $doc->save();

                // Our thumbnail was saved, so let's remove the old one.

                // Only do this if the name has changed, or we'll remove the
                // image we just uploaded.
                if ($previousThumbnail !== $doc->thumbnail)
                {
                  // We just want the base name, not the resized one.
                  $imagePath = $doc->getImagePathFromUrl($previousThumbnail, true);

                  if (Storage::has($imagePath)) {
                    Storage::delete($imagePath);
                  }
                  foreach ($sizes as $name => $size) {
                    $imagePath = $doc->addSizeToImage($imagePath, $size);
                    if (Storage::has($imagePath)) {
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

    public function deleteImage(DocAccessEditRequest $request, Doc $doc)
    {
        $image_path = $doc->getImagePathFromUrl($doc->thumbnail);

        if (Storage::has($image_path)) {
            try {
                Storage::delete($image_path);
            } catch (Exception $e) {
                Log::error("Error deleting document featured image for document id {$doc->id}");
                Log::error($e);
            }
        }
        $doc->thumbnail = null;
        $doc->save();
        return Response::json($this->growlMessage('Image deleted successfully', 'success'));
    }

    public function getUserDocuments(User $user)
    {
        $groups = $user->groups;
        $groupedDocs = [];

        foreach ($groups as $group) {
            $tempDocs = $group->docs()->get()->toArray();
            array_push($groupedDocs, ['name' => $group->name, 'docs' => $tempDocs]);
        }

        return Response::json([ 'groups' => $groupedDocs ]);
    }

    public function getSocialDoc(DocAccessReadRequest $request, Doc $doc)
    {
        $content = [
            'title' => $doc->title,
            'description' => $doc->introtext()->first()['meta_value'],
            'image' => $doc->thumbnail
        ];

        return view('layouts.social', $content);
    }

    public function getActivity(DocAccessEditRequest $request, Doc $doc)
    {
        $excludeUserIds = [];
        if ($request->query('exclude_sponsors') && $request->query('exclude_sponsors') !== 'false') {
            $excludeUserIds = $doc->sponsorIds;
        }

        $statistics = [
            'comments' => [],
            'notes' => [],
        ];

        $now = \Carbon\Carbon::now();
        $baseQuery = $doc->allComments()->whereNotIn('user_id', $excludeUserIds);
        $statsFor = function ($query, $time) {
            $query = clone $query;
            return $query
                ->where('created_at', '>=', $time)
                ->count()
                ;
        };
        foreach (['notes', 'comments'] as $key) {
            $query = clone $baseQuery;
            if ($key === 'notes') {
                $query->onlyNotes();
            } else {
                $query->notNotes();
            }

            $statistics[$key]['total'] = $query->count();
            $statistics[$key]['month'] = $statsFor($query, $now->copy()->subMonth());
            $statistics[$key]['week'] = $statsFor($query, $now->copy()->subWeek());
            $statistics[$key]['day'] = $statsFor($query, $now->copy()->subDay());
        }

        return Response::json($statistics);
    }
}
