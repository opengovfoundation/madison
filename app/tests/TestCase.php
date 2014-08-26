<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	* Default preparation for each test
	*/
	public function setUp(){
		parent::setUp();

		$this->prepareForTests();
	}

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

	/**
	* Run before each test
	* 	Sets Mail::pretend(true)
	*/
	public function prepareForTests(){
		Mail::pretend(true);
	}

	/**
	*	Resets the database
	*/
	protected function db_reset(){
    Artisan::call('db:clear');
    Artisan::call('migrate');    
  }
}
