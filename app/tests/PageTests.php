<?php

class PagesTest extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Tests Static Pages
	 */
	public function testStaticPages(){
		$this->call('GET', '/');
		$this->assertResponseOk();

		$this->call('GET', '/about');
		$this->assertResponseOk();

		$this->call('GET', '/faq');
		$this->assertResponseOk();
	}
}
