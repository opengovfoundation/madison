<?php

class DocumentsController extends Controller
{
	public function listDocuments()
	{
		if(!Auth::check()) {
			return Redirect::to('/')->with('error', 'You must be logged in');
		}
		
		$docs = Doc::allOwnedBy(Auth::user()->id);
		
		return View::make('documents.list', $docs);
	}
}