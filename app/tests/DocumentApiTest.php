<?php

class DocumentApiTest extends TestCase {

	/**
	 * Tests Static Pages
	 */
	public function testGetDocs(){
		$this->call('GET', '/api/docs');
		$this->assertResponseOk();
	}
}
