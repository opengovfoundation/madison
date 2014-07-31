<?php

/**
* Sitemap Route
* TODO: What are the performance implications of this?  Are the results cached?  I would assume so, but not sure.
*/
Route::get('sitemap', function(){

  $sitemap = App::make('sitemap');

  $pages = array('about', 'faq', 'user/login', 'user/signup');

  foreach($pages as $page){
    $sitemap->add($page);
  }

    $docs = Doc::all();

    foreach ($docs as $doc)
    {
        $sitemap->add('docs/'.$doc->slug);
    }

    $annotations = Annotation::all();

    foreach($annotations as $annotation){
      $sitemap->add('annotation/'.$annotation->id);
    }

    $users = User::all();

    foreach($users as $user){
      $sitemap->add('user/'.$user->id);
    }

    // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
    return $sitemap->render('xml');
});

