<?php
class Note extends Eloquent{
	public static $timestamp = true;
	
	public $includes = array('user');
	
	public function doc_content(){
		return $this->belongs_to('DocContent', 'section_id');
	}
	
	public function note_children(){
		return $this->has_many('Note', 'parent_id');
	}
	
	public function note_parent(){
		return $this->belongs_to('Note', 'parent_id');
	}
	
	public function doc(){
		return $this->belongs_to('Doc', 'doc_id');
	}
	
	public function user(){
		return $this->belongs_to('User');
	}
	
	public function meta(){
		return $this->has_many('NoteMeta');
	}
	
	public function setUserMeta(){
		$metas = $this->has_many('NoteMeta')->where('user_id', '=', Auth::user()->id)->get();
		
		$metaArray = array(
			'like'		=> 0,
			'dislike'	=> 0,
			'flag'		=> 0
		);
		
		foreach($metas as $meta){
			$metaArray[$meta->meta_value] = 1;
		}
		
		$this->usermeta = $metaArray;
		
		return true;
	}
	
	public function add_note_feedback($meta_type, $user_id){
		$types = array('like', 'dislike', 'flag');
		
		if(!in_array($meta_type, $types)){
			throw new Exception('Invalid feedback type');
		}
		
		$note_meta = NoteMeta::where('meta_key', '=', 'note_feedback')
								->where('note_id', '=', $this->id)
								->where('user_id', '=', $user_id)
								->first();
								
		if(!empty($note_meta)){//The user has already provided feedback on this Note
			if($note_meta->meta_value == $meta_type){//The user is removing their feedback
				//pluralize attribute
				$attr = $meta_type . 's';
				
				//decrement note feedback value
				$this->$attr--;
				
				//Save Note and Delete NoteMeta
				$this->save();
				$note_meta->delete();
				
				return 0;
			}else{
				//pluralize attributes
				$prevAttr = $note_meta->meta_value . 's';
				$newAttr = $meta_type . 's';
				
				//change Note feedback counts
				$this->$prevAttr--;
				$this->$newAttr++;
				
				//change user's feedback type
				$note_meta->meta_value = $meta_type;
			}	
		}else{//Create new note_feedback for this note from this user
			
			//Create new NoteMeta
			$note_meta = new NoteMeta();
			$note_meta->note_id = $this->id;
			$note_meta->user_id = $user_id;
			$note_meta->meta_key = 'note_feedback';
			$note_meta->meta_value = $meta_type;
			
			//Update this Note
			$attr = $meta_type . 's';//Note feedback attributes are pluralized
			$this->$attr++;
		}
		
		//Save NoteMeta and Note
		$note_meta->save();
		$this->save();
		return 1;
	}
}
