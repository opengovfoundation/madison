<?php

use Way\Tests\Assert;
use Way\Tests\Should;
use Zizaco\FactoryMuff\Facade\FactoryMuff;

class AnnotationTest extends TestCase {

	public function test_relation_with_author(){
		//Instantiate, fill with values, save and return
		$annotation = FactoryMuff::create('Annotation');

		//Thanks to FactoryMuff, this $annotation has an author
		$this->assertEquals($annotation->user_id, $post->user->id);
	}
	//TODO: methods to test
		//getEsClient
		//setEsIndex
		//getEsIndex
		//connectToEs
		//user
		//comments
		//tags
		//permissions
		//createFromAnnotatorArray
		//toAnnotatorArray
		//loadAnnotationsForAnnotator
		//updateSearchIndex
		//delete
		//addOrUpdateComment
		//getMetaCount
		//saveUserAction
		//likes
		//dislikes
		//flags
	
}
