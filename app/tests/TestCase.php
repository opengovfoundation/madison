<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

	public function testStaticPages(){
		$this->call('GET', '/');
		$this->assertResponseOk();

		$this->call('GET', '/about');
		$this->assertResponseOk();

		$this->call('GET', '/faq');
		$this->assertResponseOk();


	}	

}
