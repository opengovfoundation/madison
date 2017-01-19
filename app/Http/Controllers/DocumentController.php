<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document as Requests;
use App\Models\Category;
use App\Models\User;
use App\Models\Doc as Document;
use App\Models\DocContent as DocumentContent;
use App\Models\DocMeta as DocumentMeta;
use App\Models\Sponsor;
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
        // documents on a per-sponsor basis. For any given sponsor any one can see
        // the sponsors documents that are published, but for users that belong
        // to a sponsor, they can also see the documents in their sponsors in
        // other publish states.
        //
        // As the number of sponsors in the systems grows so does the size of
        // this query, that could become an issue at some point.
        //
        // Default behavior should be to filter to only documents that are
        // public or the user owns (i.e., they are a member of the sponsor that
        // owns them with sufficient privileges to view the document in it's
        // current state)
        //
        // If the user specifies publish states and no sponsors, view all
        // published documents (if that was a publish state requested) and the
        // publish states allowed for each sponsor the user belongs to
        //
        // If the user specifies some sponsors but no explicit publish states,
        // should show every document visible to the user in those sponsors, for
        // some they might be able to see all the publish states, for others
        // maybe only published (e.g., the sponsors they are not a part of)
        //
        // If the user specifies some of both, then of course we want to
        // restrict ourselves to only documents that belong to that sponsor and
        // within those, only the ones they have sufficient permission to view
        // for the each sponsor

        // grab the sponsor ids we want to concern ourselves with, by default we
        // don't want to limit ourselves at all, i.e., we want to make
        // available all possible documents, so we default to all sponsors
        $sponsorIds = [];
        if (!$request->has('sponsor_id')) {
            $sponsorIds = Sponsor::select('id')->pluck('id')->toArray();
        } else {
            $sponsorIds = $request->input('sponsor_id');
        }

        // if the user is logged in, lookup any sponsors they belong to so we
        // can widen the possible publish states we will allow for those sponsor
        // documents
        $userSponsorIds = [];
        if ($request->user()) {
            if ($request->user()->isAdmin()) {
                // we'll just act like an admin is a member of every sponsor
                $userSponsorIds = Sponsor::select('id')->pluck('id')->flip()->toArray();
            } else {
                $userSponsorIds = $request->user()->sponsors()->pluck('sponsors.id')->flip()->toArray();
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

        // build up a map of which publish states the user can see for each sponsor
        $sponsorIdsToPubStates = [];
        foreach ($sponsorIds as $sponsorId) {
            $pubStates = [];
            // by default, you can only see published documents
            $possiblePubStates = [Document::PUBLISH_STATE_PUBLISHED];
            if (isset($userSponsorIds[$sponsorId])) {
                // if you are a member of the sponsor in any role, you can see
                // the document in whatever state it's in
                $possiblePubStates = Document::validPublishStates();
            }
            $pubStates = array_intersect($possiblePubStates, $requestedPublishStates);
            $sponsorIdsToPubStates[$sponsorId] = $pubStates;
        }

        // here's the actual query part, restricting the selected documents
        // to only those the user has permission to see
        $documentsQuery->where(function ($documentsQuery) use ($sponsorIdsToPubStates) {
            // add an OR clause for every requested sponsor and publish states combo
            foreach ($sponsorIdsToPubStates as $sponsorId => $pubStates) {
                $documentsQuery->orWhere(function ($query) use ($sponsorId, $pubStates) {
                    $query->whereHas('sponsors', function ($q) use ($sponsorId, $pubStates) {
                        $q->where('id', $sponsorId);
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
        $sponsors = Sponsor::where('status', Sponsor::STATUS_ACTIVE)->get();
        $publishStates = static::validPublishStatesForQuery();
        $discussionStates = Document::validDiscussionStates();

        // draw the page
        return view('documents.list', compact([
            'documents',
            'documentsCapabilities',
            'categories',
            'sponsors',
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

        $availableSponsors = $user->sponsors;
        $availableSponsors->filter(function ($sponsor) use ($user) {
            return $sponsor->userCanCreateDocument($user);
        });

        $activeSponsor = $request->user()->activeSponsor();
        if ($activeSponsor && !$activeSponsor->userCanCreateDocument($user)) {
            $activeSponsor = null;
        }

        return view('documents.create', compact('availableSponsors', 'activeSponsor'));
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

        $document->sponsors()->sync([$request->input('sponsor_id')]);

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
        $commentCount = $document->comment_count;
        $noteCount = $document->note_count;
        $userCount = $document->user_count;
        $supportCount = $document->support;
        $opposeCount = $document->oppose;
        $userSupport = null;

        // Get current user support status, if logged in
        if ($request->user()) {
            $existingSupportMeta = $this->getUserSupportMeta($request->user(), $document);

            if ($existingSupportMeta) {
                $userSupport = (bool) $existingSupportMeta->meta_value;
            }
        }

        $documentPages = $document->content()->paginate(1);
        $comments = $document->comments()->notNotes()->paginate(15, ['*'], 'comment_page');

        return view('documents.show', compact([
            'document',
            'documentPages',
            'comments',
            'commentCount',
            'noteCount',
            'userCount',
            'supportCount',
            'opposeCount',
            'userSupport',
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
        $sponsors = Sponsor::where('status', Sponsor::STATUS_ACTIVE)->get();
        $publishStates = Document::validPublishStates();
        $discussionStates = Document::validDiscussionStates();
        $pages = $document->content()->paginate(1);

        return view('documents.edit', compact([
            'document',
            'categories',
            'sponsors',
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
        $document->sponsors()->sync([$request->input('sponsor_id')]);
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

    public function updateSupport(Requests\PutSupport $request, Document $document)
    {
        $support = (bool) $request->input('support');

        $existingDocumentMeta = $this->getUserSupportMeta($request->user(), $document);

        if ($existingDocumentMeta) {

            // are we removing support/opposition?
            if ((bool) $existingDocumentMeta->meta_value === $support) {
                $existingDocumentMeta->forceDelete();
            } else {
                $existingDocumentMeta->meta_value = $support;
                $existingDocumentMeta->save();
            }
        } else {
            // create new one!
            $documentMeta = new DocumentMeta();
            $documentMeta->doc_id = $document->id;
            $documentMeta->user_id = $request->user()->id;
            $documentMeta->meta_key = 'support';
            $documentMeta->meta_value = $support;
            $documentMeta->save();
        }

        flash(trans('messages.document.update_support'));
        return redirect()->route('documents.show', ['document' => $document->slug]);
    }

    public static function validPublishStatesForQuery()
    {
       return ['all'] + Document::validPublishStates();
    }

    protected function getUserSupportMeta(User $user, Document $document)
    {
        return DocumentMeta::where('user_id', $user->id)
            ->where('meta_key', '=', 'support')
            ->where('doc_id', '=', $document->id)
            ->first();
    }
}
