<?php
/**
 * 	User Model
 */
class User extends Eloquent{
	public static $timestamp = true;
	
	//Notes this user has created
	public function notes(){
		return $this->has_many('Note');
	}
	
	//This user's organization
	public function organization(){
		return $this->belongs_to('Organization');
	}
	
	public function note_meta(){
		return $this->has_many('NoteMeta');
	}
	
	public function setSuggestions(){
		$suggestions = $this->has_many('Note')->where('type', '=', 'suggestion')->get();
		
		foreach($suggestions as $suggestion){
			$suggestion->orig_content = DocContent::find($suggestion->section_id)->content;
		}
		
		$this->suggestions = $suggestions;
		
		return true;
	}
	
	public function comments(){
		return $this->has_many('Note')->where('type', '=', 'comment');
	}
}

