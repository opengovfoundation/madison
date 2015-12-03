<?php

namespace App\Http\Controllers;

use Auth;
use Response;
use Feed;
use Input;
use App\Models\Doc;
use App\Models\DocMeta;

/**
 * 	Controller for Document actions.
 */
class DocController extends Controller
{
    public $restful = true;

    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter('auth', array('on' => array('post', 'put', 'delete')));
    }

    public function getEmbedded($slug = null)
    {
        $doc = Doc::findDocBySlug($slug);

        if (is_null($doc)) {
            App::abort('404');
        }

        $view = View::make('doc.reader.embed', compact('doc'));

        return $view;
    }

    /**
     *	Method to handle posting support/oppose clicks on a document.
     *
     * @param int $doc
     *
     * @return json array
     */
    public function postSupport($doc)
    {
        $input = Input::get();

        $supported = (bool) $input['support'];

        $docMeta = DocMeta::withTrashed()->where('user_id', Auth::user()->id)->where('meta_key', '=', 'support')->where('doc_id', '=', $doc)->first();

        if (!isset($docMeta)) {
            $docMeta = new DocMeta();
            $docMeta->doc_id = $doc;
            $docMeta->user_id = Auth::user()->id;
            $docMeta->meta_key = 'support';
            $docMeta->meta_value = (string) $supported;
            $docMeta->save();
        } elseif ($docMeta->meta_value == (string) $supported && !$docMeta->trashed()) {
            $docMeta->delete();
            $supported = null;
        } else {
            if ($docMeta->trashed()) {
                $docMeta->restore();
            }
            $docMeta->doc_id = $doc;
            $docMeta->user_id = Auth::user()->id;
            $docMeta->meta_key = 'support';
            $docMeta->meta_value = (string) (bool) $input['support'];
            $docMeta->save();
        }

        $supports = DocMeta::where('meta_key', '=', 'support')->where('meta_value', '=', '1')->where('doc_id', '=', $doc)->count();
        $opposes = DocMeta::where('meta_key', '=', 'support')->where('meta_value', '=', '')->where('doc_id', '=', $doc)->count();

        return Response::json(array('support' => $supported, 'supports' => $supports, 'opposes' => $opposes));
    }

    /**
     *	Method to handle document RSS feeds.
     *
     *	@param string $slug
     *
     * @return view $feed->render()
     */
    public function getFeed($slug)
    {
        $doc = Doc::where('slug', $slug)->with('comments', 'annotations', 'userSponsor', 'groupSponsor')->first();

        $feed = Feed::make();

        $feed->title = $doc->title;
        $feed->description = "Activity feed for '".$doc->title."'";
        $feed->link = URL::to('docs/'.$slug);
        $feed->pubdate = $doc->updated_at;
        $feed->lang = 'en';

        $activities = $doc->comments->merge($doc->annotations);

        $activities = $activities->sort(function ($a, $b) {
            return (strtotime($a['updated_at']) > strtotime($b['updated_at'])) ? -1 : 1;
        });

        foreach ($activities as $activity) {
            $item = $activity->getFeedItem();

            array_push($feed->items, $item);
        }

        return $feed->render('atom');
    }
}
