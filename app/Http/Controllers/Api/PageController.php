<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Page;
use App\Models\PageContent;
use App\Http\Requests\Api\StorePageRequest;
use App\Http\Requests\Api\UpdatePageRequest;
use App\Http\Requests\Api\DestroyPageRequest;
use App\Http\Requests\Api\UpdatePageContentRequest;

class PageController extends Controller
{
    public $restful = true;

    /**
     * List all pages.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $pageQuery = Page::query();

        if ($request->has('header_nav_link')) $pageQuery->where('header_nav_link', true);
        if ($request->has('footer_nav_link')) $pageQuery->where('footer_nav_link', true);
        if ($request->has('external')) $pageQuery->where('external', $request->input('external'));

        $pages = $pageQuery->get();
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
        $pageContent = PageContent::create([
            'page_id' => $page->id,
            'content' => 'New page content'
        ]);
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
        $format = $request->input('format');

        if (!isset($format) || $format === 'markdown') {
            $content = $page->content->markdown();
        } else {
            $content = $page->content->html();
        }

        return response()->json([ 'content' => $content ]);
    }

    /**
     * Update content for a page.
     *
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function updateContent(UpdatePageContentRequest $request, Page $page)
    {
        $page->content->update([ 'content' => $request->input('content') ]);
        return response()->json([ 'content' => $page->content->markdown() ]);
    }

}
