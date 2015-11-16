<?php

class DocumentsController extends BaseController
{
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
        $docContent = DocContent::where('doc_id', $id)->first();

        $returned = array();

        if($docContent)
        {
            $returned['raw'] = $docContent->content;
            $returned['html'] = $docContent->html();
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
            DB::transaction(function () use ($docContent, $content, $documentId, $document) {
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

    public function uploadImage($docId)
    {
        if (Input::hasFile('file')) {
            $file = Input::file('file');

            $extension = $file->guessExtension();

            $filename = "default.$extension";
            $public_directory = "/img/doc-".$docId."/";
            $web_path = $public_directory.$filename;

            $path = public_path().$public_directory;

            $doc = Doc::where('id', $docId)->first();
            $doc->thumbnail = $web_path;

            try {
                $doc->save();

                $file->move($path, $filename);
            } catch (Exception $e) {
                return Response::json($this->growlMessage('There was an error with the image upload', 'error'), 500);
            }

            $params = [
                'imagePath' => $web_path,
            ];

            return Response::json($this->growlMessage("Upload successful", 'success', $params));
        } else {
            return Response::json($this->growlMessage("There was an error uploading your image.", 'error'));
        }
    }

    public function deleteImage($docId)
    {
        $doc = Doc::where('id', $docId)->first();

        $image_path = public_path().$doc->thumbnail;

        try {
            File::delete($image_path);
            $doc->thumbnail = null;
            $doc->save();
        } catch (Exception $e) {
            Log::error("Error deleting document featured image for document id $docId");
            Log::error($e);
        }

        return Response::json($this->growlMessage('Image deleted successfully', 'success'));
    }

    public function getFeatured()
    {
        $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

        $doc = Doc::with('categories')->with('sponsor')->with('statuses')->with('dates');

        if ($featuredSetting) {
            $featuredId = (int) $featuredSetting->meta_value;
            $doc = $doc->where('id', $featuredId)
                ->where('private', '!=', '1')
                ->where('is_template', '!=', '1')
                ->first();
        } else {
            $doc = $doc->orderBy('created_at', 'desc')
                ->where('private', '!=', '1')
                ->where('is_template', '!=', '1')
                ->first();
            $doc->thumbnail = '/img/default/default.jpg';
        }
        $doc->enableCounts();

        $return_doc = $doc->toArray();

        $return_doc['introtext'] = $doc->introtext()->first()['meta_value'];

        $return_doc['updated_at'] = date('c', strtotime($return_doc['updated_at']));
        $return_doc['created_at'] = date('c', strtotime($return_doc['created_at']));

        return Response::json($return_doc);
    }

    public function postFeatured()
    {
        if (!Auth::user()->hasRole('Admin')) {
            return Response::json($this->growlMessage('You are not authorized to change the Featured Document.', 'error'), 403);
        }

        $docId = Input::get('id');

        try {
            $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();
            $featuredSetting->meta_value = $docId;
            $featuredSetting->save();
        } catch (Exception $e) {
            return Response::json($this->growlMessage('There was an error updating the Featured Document', 'error'), 500);
        }

        return Response::json($this->growlMessage('Featured Document saved successfully.', 'success'));
    }
}
