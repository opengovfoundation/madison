<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageContent;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Requests\DestroyPageRequest;

class PageController extends Controller
{
    public $restful = true;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List all pages.
     *
     * @return Response
     */
    public function index()
    {
        $pages = Page::all();
        return response()->json($pages);
    }

    /**
     * See an individual page
     *
     * @param $page_id
     * @return Response
     */
    public function show(Page $page)
    {
        return response()->json($page);
    }

    /**
     * Create a new page.
     *
     * @param StorePageRequest $request
     * @return Response
     */
    public function store(StorePageRequest $request)
    {
        $page = Page::create($request->all());
        return response()->json($page);
    }

    /**
     * Update a page.
     *
     * @param Page $page
     * @param UpdatePageRequest $request
     * @return Response
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $page->update($request->all());
        return response()->json($page);
    }

    /**
     * Destroy a page.
     *
     * @param DestroyPageRequest $request
     * @param Page $page
     * @return Response
     */

    public function destroy(DestroyPageRequest $request, Page $page)
    {
        $page->delete();
        return response()->json($page);
    }

    /**
     * Get content for a page.
     *
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function getContent(Request $request, Page $page)
    {
        // get content for specific page
    }

}
