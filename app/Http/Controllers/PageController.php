<?php

namespace App\Http\Controllers;


class PageController extends Controller
{
    public $restful = true;

    /**
     * Home Page.
     */
    public function home()
    {
        return View::make('single');
    }

    /**
     * About Page.
     */
    public function getAbout()
    {
        return View::make('single');
    }

    /**
     * FAQ Page.
     */
    public function faq()
    {
        return View::make('single');
    }

    public function privacyPolicy()
    {
        return View::make('single');
    }

    public function terms()
    {
        return View::make('single');
    }

    public function copyright()
    {
        return View::make('single');
    }
}
