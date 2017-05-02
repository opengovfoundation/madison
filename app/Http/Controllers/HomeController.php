<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doc as Document;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $featuredDocuments = Document::getFeatured();
        $popularDocuments = Document::getActiveOrRecent(3);

        return view('home', compact([
            'featuredDocuments',
            'popularDocuments',
        ]));
    }
}
