<?php

use Way\Tests\Assert;
use Way\Tests\Should;

class PageTest extends TestCase {

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
