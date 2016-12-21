<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document as Requests;
use App\Models\Category;
use App\Models\Doc as Document;
use App\Models\DocContent as DocumentContent;
use App\Models\DocMeta as DocumentMeta;
use App\Models\Group;
use App\Services;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Storage;

class DocumentController extends Controller
{
    protected $documentService;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Services\Documents $documentService)
    {
        $this->documentService = $documentService;

        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Requests\Index $request)
    {
        $orderField = $request->input('order', 'updated_at');
        $orderDir = $request->input('order_dir', 'DESC');
        $discussionStates = $request->input('discussion_state', null);

        $documentsQuery = Document
            ::where('is_template', '!=', '1');

        if ($discussionStates) {
            $documentsQuery->whereIn('discussion_state', $discussionStates);
        }

        if ($request->has('category_id') && !in_array('any', $request->input('category_id'))) {
            $documentsQuery->whereHas('categories', function ($q) use ($request) {
                $ids = $request->input('category_id');
                $q->whereIn('categories.id', $ids);
            });
        } elseif ($request->has('category')) {
            $documentsQuery->whereHas('categories', function ($q) use ($request) {
                $category = $request->input('category');
                $q->where('categories.name', 'LIKE', "%$category%");
            });
        }

        if ($request->has('title')) {
            $title = $request->get('title');
            $documentsQuery->where('title', 'LIKE', "%$title%");
        }

        // So this part of the query is a little crazy. It basically grabs
        // documents on a per-group basis. For any given group any one can see
        // the groups documents that are published, but for users that belong
        // to a group, they can also see the documents in their groups in
        // other publish states.
        //
        // As the number of groups in the systems grows so does the size of
        // this query, that could become an issue at some point.
        //
        // Default behavior should be to filter to only documents that are
        // public or the user owns (i.e., they are a member of the group that
        // owns them with sufficient privileges to view the document in it's
        // current state)
        //
        // If the user specifies publish states and no groups, view all
        // published documents (if that was a publish state requested) and the
        // publish states allowed for each group the user belongs to
        //
        // If the user specifies some groups but no explicit publish states,
        // should show every document visible to the user in those groups, for
        // some they might be able to see all the publish states, for others
        // maybe only published (e.g., the groups they are not a part of)
        //
        // If the user specifies some of both, then of course we want to
        // restrict ourselves to only documents that belong to that group and
        // within those, only the ones they have sufficient permission to view
        // for the each group

        // grab the group ids we want to concern ourselves with, by default we
        // don't want to limit ourselves at all, i.e., we want to make
        // available all possible documents, so we default to all groups
        $groupIds = [];
        if (!$request->has('group_id')) {
            $groupIds = Group::select('id')->pluck('id')->toArray();
        } else {
            $groupIds = $request->input('group_id');
        }

        // if the user is logged in, lookup any groups they belong to so we
        // can widen the possible publish states we will allow for those group
        // documents
        $userGroupIds = [];
        if ($request->user()) {
            if ($request->user()->isAdmin()) {
                // we'll just act like an admin is a member of every group
                $userGroupIds = Group::select('id')->pluck('id')->flip()->toArray();
            } else {
                $userGroupIds = $request->user()->groups()->pluck('groups.id')->flip()->toArray();
            }
        }

        // grab all the publish states we want to consider, by default we'll
        // include all non-deleted states
        $requestedPublishStates = [];
        if (!$request->has('publish_state')) {
            $requestedPublishStates = [
                Document::PUBLISH_STATE_PUBLISHED,
                Document::PUBLISH_STATE_UNPUBLISHED,
                Document::PUBLISH_STATE_PRIVATE,
            ];
        } elseif ($request->has('publish_state') && in_array('all', $request->input('publish_state'))) {
            $requestedPublishStates = Document::validPublishStates();
        } else {
            $requestedPublishStates = $request->input('publish_state');
        }

        if (in_array(Document::PUBLISH_STATE_DELETED_ADMIN, $requestedPublishStates)
            || in_array(Document::PUBLISH_STATE_DELETED_USER, $requestedPublishStates)) {
            $documentsQuery->withTrashed();
        }

        // build up a map of which publish states the user can see for each group
        $groupIdsToPubStates = [];
        foreach ($groupIds as $groupId) {
            $pubStates = [];
            // by default, you can only see published documents
            $possiblePubStates = [Document::PUBLISH_STATE_PUBLISHED];
            if (isset($userGroupIds[$groupId])) {
                // if you are a member of the group in any role, you can see
                // the document in whatever state it's in
                $possiblePubStates = Document::validPublishStates();
            }
            $pubStates = array_intersect($possiblePubStates, $requestedPublishStates);
            $groupIdsToPubStates[$groupId] = $pubStates;
        }

        // here's the actual query part, restricting the selected documents
        // to only those the user has permission to see
        $documentsQuery->where(function ($documentsQuery) use ($groupIdsToPubStates) {
            // add an OR clause for every requested group and publish states combo
            foreach ($groupIdsToPubStates as $groupId => $pubStates) {
                $documentsQuery->orWhere(function ($query) use ($groupId, $pubStates) {
                    $query->whereHas('sponsors', function ($q) use ($groupId, $pubStates) {
                        $q->where('id', $groupId);
                    });
                    $query->whereIn('publish_state', $pubStates);
                });
            }
        });

        // execute the query
        $documents = null;
        $limit = $request->input('limit', 10);

        if ($orderField === 'activity') {
            // ordering by activity is special

            // we limit the query to only the documents that we have activity
            // data on, which currently means published documents with open
            // discussion states, we could not do this and simply have all
            // other documents sorted to the bottom instead of excluded
            $unorderedDocuments = $documentsQuery
                ->whereIn('id', Document::getActiveIds())
                ->get()
                ;

            $offset = $request->input('page', 0) * $limit;
            $orderedAndLimitedDocuments = Document::sortByActive($unorderedDocuments)
                ->slice($offset, $limit);

            $documents = new LengthAwarePaginator(
                $orderedAndLimitedDocuments,
                count(Document::getActiveIds()), // total items possible
                $limit,
                Paginator::resolveCurrentPage(),
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => 'page'
                ]
            );
        } else {
            $documents = $documentsQuery
                ->orderby($orderField, $orderDir)
                ->paginate($limit);
        }

        $documentsCapabilities = [];
        $baseDocumentCapabilities = [
            'open' => true,
            'edit' => false,
            'delete' => false,
            'restore' => false,
        ];
        foreach ($documents as $document) {
            $caps = $baseDocumentCapabilities;

            if ($document->publish_state === Document::PUBLISH_STATE_DELETED_ADMIN
                || $document->publish_state === Document::PUBLISH_STATE_DELETED_USER
            ) {
                $caps = array_map(function ($item) { return false; }, $caps);
                $caps['restore'] = true;
            } elseif ($request->user()
                      && ($request->user()->isAdmin()
                          || $document->canUserEdit($request->user())
                         )
            ) {
                    $caps = array_map(function ($item) { return true; }, $caps);
                    $caps['restore'] = false;
            }

            $documentsCapabilities[$document->id] = $caps;
        }

        // for the query builder modal
        $categories = Category::all();
        $groups = Group::where('status', Group::STATUS_ACTIVE)->get();
        $publishStates = static::validPublishStatesForQuery();
        $discussionStates = Document::validDiscussionStates();

        // draw the page
        return view('documents.list', compact([
            'documents',
            'documentsCapabilities',
            'categories',
            'groups',
            'publishStates',
            'discussionStates',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = $request->user();

        $availableGroups = $user->groups;
        $availableGroups->filter(function ($group) use ($user) {
            return $group->userCanCreateDocument($user);
        });

        $activeGroup = $request->user()->activeGroup();
        if ($activeGroup && !$activeGroup->userCanCreateDocument($user)) {
            $activeGroup = null;
        }

        return view('documents.create', compact('availableGroups', 'activeGroup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\Store $request)
    {
        $title = $request->input('title');
        $slug = str_slug($title, '-');

        // If the slug is taken
        if (Document::where('slug', $slug)->count()) {
            $counter = 0;
            $tooMany = 10;
            do {
                if ($counter > $tooMany) {
                    flash(trans('messages.document.title_invalid'));
                    return back()->withInput();
                }
                $counter++;
                $new_slug = $slug . '-' . str_random(8);
            } while (Document::where('slug', $new_slug)->count());

            $slug = $new_slug;
        }

        $document = new Document();
        $document->title = $title;
        $document->slug = $slug;
        $document->save();

        $document->sponsors()->sync([$request->input('group_id')]);

        flash(trans('messages.document.created'));
        return redirect()->route('documents.edit', ['document' => $document->slug]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Requests\View $request, Document $document)
    {
        $pages = $document->content()->paginate(1);

        return view('documents.show', compact([
            'document',
            'pages',
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Requests\Edit $request, Document $document)
    {
        $categories = Category::all();
        $groups = Group::where('status', Group::STATUS_ACTIVE)->get();
        $publishStates = Document::validPublishStates();
        $discussionStates = Document::validDiscussionStates();
        $pages = $document->content()->paginate(1);

        return view('documents.edit', compact([
            'document',
            'categories',
            'groups',
            'publishStates',
            'discussionStates',
            'pages',
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Update $request, Document $document)
    {
        $document->update($request->all());
        $document->setIntroText($request->input('introtext'));
        $document->sponsors()->sync([$request->input('group_id')]);
        $document->syncCategories($request->input('category_id'));

        // update content for correct page
        $pageContent = $document->content()->where('page', $request->input('page', 1))->first();

        if ($pageContent) {
            $pageContent->content = $request->input('page_content', '');
            $pageContent->save();
        }

        // feature document stuff
        if ($document->featured != (bool) $request->input('featured', false)) {
            if (!$request->user()->isAdmin()) {
                abort(403, 'Unauthorized.');
            }

            if ($request->input('featured')) {
                $document->setAsFeatured();
            } else {
                $document->removeAsFeatured();
            }
        }

        if ($request->hasFile('featured-image')) {
            if (!$request->user()->isAdmin()) {
                abort(403, 'Unauthorized.');
            }

            $file = $request->file('featured-image');

            // Keep a record of our previous featuredImage.
            $previousFeaturedImageId = $document->featuredImage;

            $imageId = $this->documentService->generateAllImageSizes($file);

            $document->featuredImage = $imageId;
            $document->save();

            // Our featured image was saved, so let's remove the old one.
            if ($previousFeaturedImageId) {
                $this->documentService->destroyAllImageSizes($previousFeaturedImageId);
            }
        }

        flash(trans('messages.document.updated'));
        return redirect()->route('documents.edit', ['document' => $document->slug]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requests\Edit $request, Document $document)
    {
        if ($request->user()->isAdmin()) {
            $document->publish_state = Document::PUBLISH_STATE_DELETED_ADMIN;
        } else {
            $document->publish_state = Document::PUBLISH_STATE_DELETED_USER;
        }

        $document->save();

        $document->annotations()->delete();
        $document->doc_meta()->delete();
        $document->content()->delete();

        $document->delete();

        $restoreUrl = route('documents.restore', ['document' => $document->slug]);
        flash(trans('messages.document.deleted', [
            'restoreLinkOpen' => "<a href='$restoreUrl'>",
            'restoreLinkClosed' => '</a>',
        ]))->important();
        return redirect()->route('documents.index');
    }

    public function restore(Requests\Edit $request, Document $document)
    {
        if ($document->publish_state === Document::PUBLISH_STATE_DELETED_ADMIN) {
            if (!$request->user()->isAdmin()) {
                abort(403, 'Unauthorized');
            }
        }

        DocumentMeta::withTrashed()->where('doc_id', $document->id)->restore();
        $document->content()->withTrashed()->restore();
        $document->annotations()->withTrashed()->restore();

        $document->restore();
        $document->publish_state = Document::PUBLISH_STATE_UNPUBLISHED;
        $document->save();

        flash(trans('messages.document.restored'));
        return redirect()->route('documents.edit', ['document' => $document->slug]);
    }

    public function storePage(Requests\Edit $request, Document $document)
    {
        $lastPage = $document->content()->max('page') ?: 0;
        $page = $lastPage + 1;

        $documentContent = new DocumentContent();
        $documentContent->content = $request->input('content', '');
        $documentContent->page = $page;
        $document->content()->save($documentContent);

        flash(trans('messages.document.page_added'));
        return redirect()->route('documents.edit', ['document' => $document->slug, 'page' => $page]);
    }

    public function showImage(Requests\View $request, Document $document, $image)
    {
        $size = $request->input('size', null);
        $imageId = $this->documentService->getImageIdForSize($image, $size);

        if (!Storage::has($imageId)) {
            abort(null, 404);
        }

        return response(Storage::get($imageId), 200)
            ->header('Content-Type', Storage::mimeType($imageId));
    }

    public function destroyImage(Requests\Edit $request, Document $document, $image)
    {
        $this->documentService->destroyAllImageSizes($image);

        if ($image === $document->featuredImage) {
            $document->featuredImage = null;
            $document->save();

            flash(trans('messages.document.featured_image_removed'));
        }

        return redirect()->route('documents.edit', ['document' => $document->slug]);
    }

    public static function validPublishStatesForQuery()
    {
       return ['all'] + Document::validPublishStates();
    }
}
