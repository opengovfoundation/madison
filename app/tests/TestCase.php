<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	* Default preparation for each test
	*/
	public function setUp(){
		parent::setUp();

		$this->prepareForTests();
	}

	public function tearDown(){
		parent::tearDown();

		$this->afterTests();
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
	*	Runs after tests have completed
	* Resets the database
	*/
	public function afterTests(){

	}

	/**
	*	Resets the database
	*/
	protected function db_reset(){
    Artisan::call('db:clear');
    Artisan::call('migrate');    
  }
}
