<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\PageContent;
use App\Http\Requests\Page as Requests;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Requests\Index $request)
    {
        $pages = Page::all();
        return view('pages.list', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Requests\Create $request)
    {
        return view('pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\Store $request)
    {
        $page = Page::create($request->all());
        $pageContent = PageContent::create([
            'page_id' => $page->id,
            'content' => 'New page content'
        ]);
        return redirect()->route('pages.edit', ['page' => $page->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Page $page)
    {
        return view('pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Requests\Edit $request, Page $page)
    {
        $pageContent = $page->content ? $page->content->content : null;

        return view('pages.edit', compact([
            'page',
            'pageContent'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Update $request, Page $page)
    {
        $page->update($request->all());

        $page->content()->update([
            'content' => $request->input('page_content')
        ]);

        flash(trans('messages.page.updated'));
        return redirect()->route('pages.edit', $page);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requests\Destroy $request, Page $page)
    {
        $page->delete();

        flash(trans('messages.page.deleted'));
        return redirect()->route('admin.pages.index');
    }
}
