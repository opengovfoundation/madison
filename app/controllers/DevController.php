<?php

class DevController extends BaseController
{
	public function getTest()
	{
		$input = array(
			'permissions' => array(
				'read' => array(),
				'update' => array(1),
				'delete' => array(1),
				'admin' => array(1)
			),
			'user' => array(
				'id' => 1,
				'email' => 'john@coggeshall.org',
				'user_level' => 1,
				'name' => "John C"
			),
			'ranges' => array(
				array(
					'start' => '/div[2]/div[1]/p[2]',
					'startOffset' => 0,
					'end' => '/div[2]/div[1]/p[2]',
					'endOffset' => 127
				)
			),
			'quote' => "This is the quote",
			'text' => "This is the comment text",
			'tags' => array('tacobell'),
			'uri' => '/docs/testing',
			'comments' => array(
				array(
					'id' => 1,
					'text' => 'This is the comment on the comment',
					'created' => "Wed, 05 Mar 2014 12:40:47 -0500",
					'updated' => "Wed, 05 Mar 2014 12:40:47 -0500",
					'user' => array(
						'id' => 2,
						'user_level' => 3,
						'email' => 'joe@blow.com',
						'name' => "Joe B"
					)
				)
			),
			'doc' => 1,
			'created' => "Wed, 05 Mar 2014 12:40:47 -0500",
			'updated' => "Wed, 05 Mar 2014 12:40:47 -0500",
			'user_action' => null,
			'flags' => 1,
			'likes' => 0,
			'dislikes' => 0
		);
		
		$annotation = Annotation::createFromAnnotatorArray($input);
		
		var_dump($annotation);
	}
	
	
}