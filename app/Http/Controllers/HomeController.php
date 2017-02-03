<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doc as Document;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories = $request->input('categories');

        $selectedCategories = [];

        $documentQuery = Document::getEager()->where('publish_state', Document::PUBLISH_STATE_PUBLISHED);

        if ($search) {
            $documentQuery->where('title', 'like', '%' . $search . '%');
        }

        if ($categories) {
            $selectedCategories = Category::whereIn('id', $categories)->get();
            $documentQuery->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        $documents = $documentQuery->paginate(5);
        $featuredDocuments = Document::getFeaturedOrRecent();
        $mostActiveDocuments = Document::getActive(6);
        $mostRecentDocuments = Document::mostRecentPublicWithOpenDiscussion()->get();

        return view('home', compact(
            'selectedCategories',
            'documents',
            'featuredDocuments',
            'mostActiveDocuments',
            'mostRecentDocuments'
        ));
    }
}
