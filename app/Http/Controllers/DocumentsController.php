<?php

namespace App\Http\Controllers;

use Event;
use Response;
use Auth;
use Input;
use Redirect;
use Storage;
use App\Models\Setting;
use App\Models\DocContent;
use App\Models\Doc;
use App\Models\MadisonEvent;

class DocumentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDocument($slug)
    {
        $doc = Doc::findDocBySlug($slug);
        $introtext = $doc->introtext()->first()['meta_value'];
        $doc->introtext = $introtext;

        $doc->enableCounts();

        return Response::json($doc);
    }

    public function getDocumentContent($id)
    {
        $page = Input::get('page', 1);
        $format = Input::get('format');
        if(!$format) {
            $format = 'html';
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

        return Response::json($returned);
    }

    public function listDocuments()
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

    public function saveDocumentEdits($documentId)
    {
        if (!Auth::check()) {
            return Redirect::to('documents')->with('error', 'You must be logged in');
        }

        $content = Input::get('content');
        $contentId = Input::get('content_id');

        if (empty($content)) {
            return Redirect::to('documents')->with('error', "You must provide content to save");
        }

        if (!empty($contentId)) {
            $docContent = DocContent::find($contentId);
        } else {
            $docContent = new DocContent();
        }

        if (!$docContent instanceof DocContent) {
            return Redirect::to('documents')->with('error', 'Could not locate document to save');
        }

        $document = Doc::find($documentId);

        if (!$document instanceof Doc) {
            return Redirect::to('documents')->with('error', "Could not locate the document");
        }

        if (!$document->canUserEdit(Auth::user())) {
            return Redirect::to('documents')->with('error', 'You are not authorized to save that document.');
        }

        $docContent->doc_id = $documentId;
        $docContent->content = $content;

        try {
            \DB::transaction(function () use ($docContent, $content, $documentId, $document) {
                $docContent->save();
            });
        } catch (\Exception $e) {
            return Redirect::to('documents')->with('error', "There was an error saving the document: {$e->getMessage()}");
        }

        //Fire document edited event for admin notifications
        $doc = Doc::find($docContent->doc_id);
        Event::fire(MadisonEvent::DOC_EDITED, $doc);

        try {
            $document->indexContent($docContent);
        } catch (\Exception $e) {
            return Redirect::to('documents')->with('error', "Document saved, but there was an error with Elasticsearch: {$e->getMessage()}");
        }

        return Redirect::to('documents')->with('success_message', 'Document Saved Successfully');
    }

    public function editDocument($documentId)
    {
        if (!Auth::check()) {
            return Redirect::to('/')->with('error', 'You must be logged in');
        }

        $doc = Doc::find($documentId);

        if (is_null($doc)) {
            return Redirect::to('documents')->with('error', 'Document not found.');
        }

        if (!$doc->canUserEdit(Auth::user())) {
            return Redirect::to('documents')->with('error', 'You are not authorized to view that document.');
        }

        return View::make('documents.edit', array(
            'page_id' => 'edit_doc',
            'page_title' => "Editing {$doc->title}",
            'doc' => $doc,
            'contentItem' => $doc->content()->where('parent_id')->first(),
        ));
    }

    public function createDocument()
    {
        if (!Auth::check()) {
            return Redirect::to('/')->with('error', 'You must be logged in');
        }

        $input = Input::all();

        $rules = array(
            'title' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return Redirect::to('documents')->withInput()->withErrors($validator);
        }

        try {
            $docOptions = array(
                'title' => $input['title'],
            );

            $user = Auth::user();

            $activeGroup = Session::get('activeGroupId');

            if ($activeGroup > 0) {
                $group = Group::where('id', '=', $activeGroup)->first();

                if (!$group) {
                    return Redirect::to('documents')->withInput()->with('error', 'Invalid Group');
                }

                if (!$group->userHasRole($user, Group::ROLE_EDITOR) && !$group->userHasRole($user, Group::ROLE_OWNER)) {
                    return Redirect::to('documents')->withInput()->with('error', 'You do not have permission to create a document for this group');
                }

                $docOptions['sponsor'] = $activeGroup;
                $docOptions['sponsorType'] = Doc::SPONSOR_TYPE_GROUP;
            } else {
                if (!$user->hasRole(Role::ROLE_INDEPENDENT_SPONSOR)) {
                    return Redirect::to('documents')->withInput()->with('error', 'You do not have permission to create a document as an individual');
                }

                $docOptions['sponsor'] = Auth::user()->id;
                $docOptions['sponsorType'] = Doc::SPONSOR_TYPE_INDIVIDUAL;
            }

            $document = Doc::createEmptyDocument($docOptions);

            if ($activeGroup > 0) {
                Event::fire(MadisonEvent::NEW_GROUP_DOCUMENT, array('document' => $document, 'group' => $group));
            }

            return Redirect::to("documents/edit/{$document->id}")->with('success_message', "Document Created Successfully");
        } catch (\Exception $e) {
            return Redirect::to("documents")->withInput()->with('error', "Sorry there was an error processing your request - {$e->getMessage()}");
        }
    }

    public function getImage($docId, $image)
    {
        $doc = Doc::where('id', $docId)->first();
        if($doc) {
            $path = $doc->getImagePath($image);
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

            $doc = Doc::where('id', $docId)->first();
            $doc->thumbnail = $doc->getImageUrl($file->getClientOriginalName());

            try {
                $doc->save();

                $path = Storage::getDriver()->getAdapter()->getPathPrefix() . $doc->getImagePath();
                $result = $file->move($path, $file->getClientOriginalName());
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

    public function getFeatured()
    {
        $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

        if ($featuredSetting) {
            // Make sure our featured document can be viewed by the public.
            $featuredIds = explode(',', $featuredSetting->meta_value);
            $docs = Doc::with('categories')
                ->with('userSponsors')
                ->with('groupSponsors')
                ->with('statuses')
                ->with('dates')
                ->whereIn('id', $featuredIds)
                ->where('publish_state', '=', Doc::PUBLISH_STATE_PUBLISHED)
                ->where('is_template', '!=', '1')
                ->get();

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
        if (empty($docs)) {
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

    public function postFeatured()
    {
        if (!Auth::user()->hasRole('Admin')) {
            return Response::json($this->growlMessage('You are not authorized to change the Featured Document.', 'error'), 403);
        }

        $docId = Input::get('id');

        try {
            $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

            $docs = explode(',', $featuredSetting->meta_value);

            if(!in_array($docId, $docs)) {
                array_unshift($docs, $docId);
            }
            $featuredSetting->meta_value = join(',', $docs);
            $featuredSetting->save();
        } catch (Exception $e) {
            return Response::json($this->growlMessage('There was an error updating the Featured Document', 'error'), 500);
        }

        return $this->getFeatured();
    }

    public function putFeatured()
    {
        if (!Auth::user()->hasRole('Admin')) {
            return Response::json($this->growlMessage('You are not authorized to change the Featured Document.', 'error'), 403);
        }

        $docs = Input::get('docs');

        try {
            $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

            $featuredSetting->meta_value = $docs;
            $featuredSetting->save();
        } catch (Exception $e) {
            return Response::json($this->growlMessage('There was an error updating the Featured Document', 'error'), 500);
        }

        return $this->getFeatured();
    }

    public function deleteFeatured($docId)
    {
        if (!Auth::user()->hasRole('Admin')) {
            return Response::json($this->growlMessage('You are not authorized to change the Featured Document.', 'error'), 403);
        }

        try {
            $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

            $docs = explode(',', $featuredSetting->meta_value);

            if(in_array($docId, $docs)) {
                $docs = array_diff($docs, array($docId));
            }
            $featuredSetting->meta_value = join(',', $docs);
            $featuredSetting->save();
        } catch (Exception $e) {
            return Response::json($this->growlMessage('There was an error updating the Featured Document', 'error'), 500);
        }

        return $this->getFeatured();
    }
}
