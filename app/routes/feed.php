<?php

/**
*   RSS Feed Route
*/
Route::get('docs/feed', function(){
    //Grab all documents
    $docs = Doc::with('comments', 'annotations', 'sponsor', 'content')->orderBy('updated_at', 'DESC')->take(20)->get();

    $feed = Feed::make();

    $feed->title = 'Madison Documents';
    $feed->description = 'Latest 20 documents in Madison';
    $feed->link = URL::to('rss');
    $feed->pubdate = $docs->first()->updated_at;
    $feed->lang = 'en';

    foreach($docs as $doc){
        $sponsor = $doc->sponsor->first();
        if($sponsor instanceof User){
            $display_name = $sponsor->fname . ' ' . $sponsor->lname;
        }else if($sponsor instanceof Group){
            $display_name = $sponsor->display_name;
        }else{
            $display_name = '';
        }

        $item = array();
        $item['title'] = $doc->title;
        $item['author'] = $display_name;
        $item['link'] = URL::to('docs/' . $doc->slug);
        $item['pubdate'] = $doc->updated_at;
        $item['description'] = $doc->title;
        $item['content'] = $doc->content->html();

        array_push($feed->items, $item);
    }

    return $feed->render('atom');

});

