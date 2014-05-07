<?php

class DocumentsController extends Controller
{
	public function listDocuments()
	{
		if(!Auth::check()) {
			return Redirect::to('/')->with('error', 'You must be logged in');
		}
		
		$docs = Doc::allOwnedBy(Auth::user()->id);
		
		return View::make('documents.list', compact('docs'));
	}
	
	public function createDocument()
	{
		if(!Auth::check()) {
			return Redirect::to('/')->with('error', 'You must be logged in');
		}
		
		$input = Input::all();
		
		$rules = array(
			'title' => 'required'
		);
		
		$validator = Validator::make($input, $rules);
		 
		if($validator->fails()) {
			return Redirect::to('documents')->withInput()->withErrors($validator);
		}
		
		try {
			
			$docOptions = array(
				'title' => $input['title']
			);
			
			$activeGroup = Session::get('activeGroupId');
			
			if($activeGroup > 0) {
				$docOptions['sponsor'] = $activeGroup;
				$docOptions['sponsorType'] = Doc::SPONSOR_TYPE_GROUP;
			} else {
				$docOptions['sponsor'] = Auth::user()->id;
				$docOptions['sponsorType'] = Doc::SPONSOR_TYPE_INDIVIDUAL;
			}
			
			$document = Doc::createEmptyDocument($docOptions);
			
			return Redirect::to("documents/edit/{$document->id}")->with('success_message', "Document Created Successfully");
		
		} catch(\Exception $e) {
			return Redirect::to("documents")->withInput()->with('error', "Sorry there was an error processing your request - {$e->getMessage()}");
		}
	}
}