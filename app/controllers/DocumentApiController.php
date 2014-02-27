<?php
/**
 * 	Controller for Document actions
 */
class DocumentApiController extends ApiController{

	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}

	public function getDoc($doc){
		$doc = Doc::with('content')->find($doc);

		return Response::json($doc);
	}

	public function postDoc($id){
		$doc = Doc::find($id);
		$doc->title = Input::get('title');
		$doc->slug = Input::get('slug');

		$doc_content = DocContent::firstOrCreate(array('doc_id' => $doc->id));

		$doc_content->content = Input::get('content.content');
		$doc_content->save();

		$doc->content(array($doc_content));

		$doc->save();

		return Response::json(Doc::with('content')->find($id));
	}

	public function getDocs(){
		$docs = Doc::with('categories')->with('sponsor')->with('statuses')->with('dates')->orderBy('updated_at', 'DESC')->get();

		foreach($docs as $doc){
			$doc->setActionCount();
		}

		return Response::json($docs);
	}

	public function getRecent($query = null){
		$recent = 10;

		if(isset($query)){
			$recent = $query;
		}

		$docs = Doc::take(10)->with('categories')->orderBy('updated_at', 'DESC')->get();

		foreach($docs as $doc){
			$doc->setActionCount();
		}

		return Response::json($docs);
	}

	public function getCategories($doc = null){
		if(!isset($doc)){
			$categories = Category::all();
		}else{
			$doc = Doc::find($doc);
			$categories = $doc->categories()->get();
		}

		return Response::json($categories);
	}

	public function postCategories($doc){
		$doc = Doc::find($doc);

		$categories = Input::get('categories');
		$categoryIds = array();

		foreach($categories as $category){
			$toAdd = Category::where('name', $category)->first();

			if(!isset($toAdd)){
				$toAdd = new Category();
			}

			$toAdd->name = $category;
			$toAdd->save();

			array_push($categoryIds, $toAdd->id);
		}

		$doc->categories()->sync($categoryIds);

		return Response::json($categoryIds);
	}

	public function getSponsor($doc){
		$doc = Doc::find($doc);

		$sponsor = $doc->sponsor()->first();

		return Response::json($sponsor);
	}

	public function postSponsor($doc){
		$sponsor = Input::get('sponsor');

		$doc = Doc::find($doc);
		$user = User::find($sponsor['id']);

		$doc->sponsor()->sync(array($user->id));

		return Response::json($user);

	}

	public function getStatus($doc){
		$doc = Doc::find($doc);

		$status = $doc->statuses()->first();

		return Response::json($status);
	}

	public function postStatus($doc){
		$status = Input::get('status');

		$doc = Doc::find($doc);

		$toAdd = Status::where('label', $status['text'])->first();

		if(!isset($toAdd)){
			$toAdd = new Status();
			$toAdd->label = $status['text'];
		}
		$toAdd->save();

		$doc->statuses()->sync(array($toAdd->id));

		return Response::json($toAdd);

	}

	public function getDates($doc){
		$doc = Doc::find($doc);

		$dates = $doc->dates()->get();

		return Response::json($dates);
	}

	public function postDate($doc){
		$doc = Doc::find($doc);

		$date = Input::get('date');

		$returned = new Date();
		$returned->label = $date['label'];
		$returned->date = date("Y-m-d H:i:s", strtotime($date['date']));
		
		$doc->dates()->save($returned);

		return Response::json($returned);
	}

	public function getAllSponsors(){
		$doc = Doc::with('sponsor')->first();
		$sponsors = $doc->sponsor;

		return Response::json($sponsors);
	}

	public function getAllStatuses(){
		$doc = Doc::with('statuses')->first();
		$statuses = $doc->status;

		return Response::json($statuses);
	}
}

