<?php

class DocumentsController extends Controller
{
	/**
	 * 	Document Creation/List or Document Edit Views
	 */
	public function getDocs($id = ''){
	
		$user = Auth::user();
	
		if(!$user->can('admin_manage_documents')) {
			return Redirect::to('/')->with('message', "You do not have permission");
		}
	
		if($id == ''){
			$docs = Doc::all();
	
			$data = array(
					'page_id'		=> 'doc_list',
					'page_title'	=> 'Edit Documents',
					'docs'			=> $docs
			);
	
			return View::make('docs.index', $data);
		}
		else{
			$doc = Doc::find($id);
			if(isset($doc)){
				$data = array(
						'page_id'		=> 'edit_doc',
						'page_title'	=> 'Edit ' . $doc->title,
						'doc'			=> $doc,
						// Just get the first content element.  We only have one, now.
						'contentItem' => $doc->content()->where('parent_id')->first()
				);
	
				return View::make('docs.edit', $data);
			}
			else{
				return Response::error('404');
			}
		}
	}
	
	/**
	 * 	Post route for creating / updating documents
	 */
	public function postDocs($id = ''){
	
		$user = Auth::user();
	
		if(!$user->can('admin_manage_documents')) {
			return Redirect::to('/')->with('message', "You do not have permission");
		}
	
		//Creating new document
		if($id == ''){
			$title = Input::get('title');
			$slug = str_replace(array(' ', '.'),array('-', ''), strtolower($title));
			$doc_details = Input::all();
	
			$rules = array('title' => 'required');
			$validation = Validator::make($doc_details, $rules);
			if($validation->fails()){
				die($validation);
				return Redirect::to('docs')->withInput()->withErrors($validation);
			}
	
			try{
				$doc = new Doc();
				$doc->title = $title;
				$doc->slug = $slug;
				$doc->save();
				$doc->sponsor()->sync(array($user->id));
	
				$starter = new DocContent();
				$starter->doc_id = $doc->id;
				$starter->content = "New Doc Content";
				$starter->save();
	
				$doc->init_section = $starter->id;
				$doc->save();
	
				return Redirect::to('docs/' . $doc->id)->with('success_message', 'Document created successfully');
			}catch(Exception $e){
				return Redirect::to('docs')->withInput()->with('error', $e->getMessage());
			}
		}
		else{
			return Response::error('404');
		}
	}
	
	/**
	 * 	PUT route for saving documents
	 */
	public function putDocs($id = ''){
	
		$user = Auth::user();
	
		if(!$user->can('admin_manage_documents')) {
			return Redirect::to('/')->with('message', "You do not have permission");
		}
	
		$content = Input::get('content');
		$content_id = Input::get('content_id');
	
		if($content_id){
			try{
				$doc_content = DocContent::find($content_id);
			}catch (Exception $e){
				return Redirect::to('docs/' . $id)->with('error', 'Error saving the document: ' . $e->getMessage());
			}
		}
		else{
			$doc_content = new DocContent();
		}
	
		$doc_content->doc_id = $id;
		$doc_content->content = $content;
		$doc_content->save();
	
		$doc = Doc::find($id);
		$doc->store_content($doc, $doc_content);
	
		return Redirect::to('docs/' . $id)->with('success_message', 'Document Saved Successfully');
	}
}