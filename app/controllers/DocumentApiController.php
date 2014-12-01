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
		$doc = Doc::with('content')->with('categories')->find($doc);

		return Response::json($doc);
	}

	public function postTitle($id){
		$doc = Doc::find($id);
		$doc->title = Input::get('title');
		$doc->save();

		$response['messages'][0] = array('text'=>'Document title saved', 'severity'=>'info');
		return Response::json($response);
	}

	public function postSlug($id){
		$doc = Doc::find($id);
		// Compare current and new slug
		$old_slug = $doc->slug;
		// If the new slug is different, save it
		if ($old_slug != Input::get('slug')) {
			$doc->slug = Input::get('slug');
			$doc->save();
			$response['messages'][0] = array('text'=>'Document slug saved', 'severity'=>'info');
		} else {
			// If the slugs are identical, the only way this could have happened is if the sanitize
			// function took out an invalid character and tried to submit an identical slug
			$response['messages'][0] = array('text'=>'Invalid slug character', 'severity'=>'error');
		}
		
		return Response::json($response);
	}

	public function postContent($id){
		$doc = Doc::find($id);
		$doc_content = DocContent::firstOrCreate(array('doc_id' => $doc->id));				
		$doc_content->content = Input::get('content');
		$doc_content->save();
		$doc->content(array($doc_content));
		$doc->save();

		Event::fire(MadisonEvent::DOC_EDITED, $doc);
		
		$response['messages'][0] = array('text'=>'Document content saved', 'severity'=>'info');
		return Response::json($response);
	}

	public function getDocs(){
		$docs = Doc::with('categories')->with('sponsor')->with('statuses')->with('dates')->orderBy('updated_at', 'DESC')->get();

		$return_docs = array();

		foreach($docs as $doc){
			// try { 
			// 	$doc->setActionCount();
			// } catch(Exception $e) {
			// 	throw $e;
			// }
			
			$return_doc = $doc->toArray();

			$return_doc['updated_at'] = date('c', strtotime($return_doc['updated_at']));
			$return_doc['created_at'] = date('c', strtotime($return_doc['created_at']));

			$return_docs[] = $return_doc;
		}

		return Response::json($return_docs);
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
			$toAdd = Category::where('name', $category['text'])->first();

			if(!isset($toAdd)){
				$toAdd = new Category();
			}

			$toAdd->name = $category['text'];
			$toAdd->save();

			array_push($categoryIds, $toAdd->id);
		}

		$doc->categories()->sync($categoryIds);
		$response['messages'][0] = array('text'=>'Categories saved', 'severity'=>'info');
		return Response::json($response);
	}

	public function hasSponsor($doc, $sponsor){
		$result = Doc::find($doc)->sponsor()->find($sponsor);
		return Response::json($result);
	} 
	

	public function getSponsor($doc){
		$doc = Doc::find($doc);
		$sponsor = $doc->sponsor()->first();

		if($sponsor){
			$sponsor->sponsorType = get_class($sponsor);	

			return Response::json($sponsor);
		}
		
		return Response::json();
		
	}

	public function postSponsor($doc){
		$sponsor = Input::get('sponsor');

		$doc = Doc::find($doc);
		$response = null;

		if(!isset($sponsor)){
			$doc->sponsor()->sync(array());
		}else{
			switch($sponsor['type']){
				case 'user':
					$user = User::find($sponsor['id']);
					$doc->userSponsor()->sync(array($user->id));
					$doc->groupSponsor()->sync(array());
					$response = $user;
					break;
				case 'group':
					$group = Group::find($sponsor['id']);
					$doc->groupSponsor()->sync(array($group->id));
					$doc->userSponsor()->sync(array());
					$response = $group;
					break;
				default:
					throw new Exception('Unknown sponsor type ' . $type);
			}
		}

		$response['messages'][0] = array('text'=>'Sponsor saved', 'severity'=>'info');
		return Response::json($response);

	}

	public function getStatus($doc){
		$doc = Doc::find($doc);

		$status = $doc->statuses()->first();

		return Response::json($status);
	}

	public function postStatus($doc){
		$toAdd = null;

		$status = Input::get('status');

		$doc = Doc::find($doc);

		if(!isset($status)){
			$doc->statuses()->sync(array());
		}else{
			$toAdd = Status::where('label', $status['text'])->first();

			if(!isset($toAdd)){
				$toAdd = new Status();
				$toAdd->label = $status['text'];
			}
			$toAdd->save();

			$doc->statuses()->sync(array($toAdd->id));
		}

		$response['messages'][0] = array('text'=>'Document saved', 'severity'=>'info');
		return Response::json($response);

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

	public function deleteDate($doc, $date){
		$date = Date::find($date);

		if(!isset($date)){
			throw new Exception("Unable to delete date.  Date id $date not found.");
		}

		$date->delete();

		return Response::json();
	}

	public function putDate($date){
		$input = Input::get('date');
		$date = Date::find($date);

		if(!isset($date)){
			throw new Exception("unable to update date.  Date id $date not found.");
		}

		$newDate = date("Y-m-d H:i:s", strtotime((string)$input['date']));
		
		$date->label = $input['label'];
		$date->date = $newDate;

		$date->save();
		
		$response['messages'][0] = array('text'=>'Document saved', 'severity'=>'info');
		return Response::json($response);
	}

	public function getAllSponsorsForUser()
	{
		$retval = array(
			'success' => false,
			'sponsors' => array(),
			'message' => ""
		);
		
		if(!Auth::check()) {
			$retval['message'] = "You must be logged in to perform this call";
			return Response::json($retval);
		}
		
		$sponsors = Auth::user()->getValidSponsors();
		
		foreach($sponsors as $sponsor) {
			
			switch(true) {
				case ($sponsor instanceof User):
					$userSponsor = $sponsor->toArray();
					$userSponsor['sponsorType'] = 'user';
					
					$retval['sponsors'][] = $userSponsor;
					
					break;
				case ($sponsor instanceof Group):
					
					$groupSponsor = $sponsor->toArray();
					$groupSponsor['sponsorType'] = 'group';
					
					$retval['sponsors'][] = $groupSponsor;
					break;
				default:
					break;
			}
			
		}
		
		$retval['success'] = true;
		
		return Response::json($retval);
	}
	
	public function getAllSponsors(){
		$doc = Doc::with('sponsor')->first();
		$sponsors = $doc->sponsor;

		return Response::json($sponsors);
	}

	public function getAllStatuses(){
		$doc = Doc::with('statuses')->first();

		$statuses = $doc->statuses;

		return Response::json($statuses);
	}
}

